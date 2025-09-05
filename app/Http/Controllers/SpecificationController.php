<?php
namespace App\Http\Controllers;

use App\Models\{Project, Specification, SpecificationVersion, SpecChangeRequest};
use Illuminate\Http\Request;

class SpecificationController extends Controller
{
    public function store(Request $req){
        $data = $req->validate([
            'project_id'=>'required|exists:projects,id',
            'code'=>'required',
            'title'=>'required',
            'body_md'=>'required',
            'attributes'=>'array'
        ]);
        $spec = Specification::create([
            'project_id'=>$data['project_id'],
            'code'=>$data['code'],
            'title'=>$data['title'],
            'status'=>'approved',
        ]);
        $v1 = SpecificationVersion::create([
            'specification_id'=>$spec->id,
            'version_no'=>1,
            'body_md'=>$data['body_md'],
            'attributes'=>$data['attributes'] ?? [],
            'created_by'=>auth()->id()
        ]);
        $spec->update(['current_version_id'=>$v1->id]);
        // project クエリパラメータを付けて show に戻す
        return redirect()->route('specifications.show', [
            'specification' => $spec->id,
            'project' => $data['project_id'],
        ]);
    }

    public function create(Request $request)
    {
        $projectId = $request->input('project');
        $project = Project::findOrFail($projectId);

        return view('specifications.create', compact('project'));
    }


    public function update(Request $req, Specification $spec){
        $data = $req->validate([
            'title'=>'required',
            'body_md'=>'required',
            'attributes'=>'array',
            'reason'=>'required',
            'impact'=>'nullable'
        ]);
        $curr = $spec->currentVersion;
        $nextNo = ($spec->versions()->max('version_no') ?? 0) + 1;
        $next = SpecificationVersion::create([
            'specification_id'=>$spec->id,
            'version_no'=>$nextNo,
            'body_md'=>$data['body_md'],
            'attributes'=>$data['attributes'] ?? [],
            'created_by'=>auth()->id()
        ]);
        $cr = SpecChangeRequest::create([
            'project_id'=>$spec->project_id,
            'specification_id'=>$spec->id,
            'from_version_id'=>$curr->id,
            'to_version_id'=>$next->id,
            'reason'=>$data['reason'],
            'impact'=>$data['impact'] ?? null,
            'status'=>'proposed',
            'requested_by'=>auth()->id(),
        ]);
        // project クエリを付けてリダイレクト
        return redirect()->route('spec-change-requests.show', [
            'cr' => $cr->id,
            'project' => $spec->project_id,
        ]);
    }

    public function approve(SpecChangeRequest $cr){
        $cr->update([
            'status'=>'approved',
            'approved_by'=>auth()->id()
        ]);
        $cr->specification->update([
            'current_version_id'=>$cr->to_version_id
        ]);
        return back();
    }

    // ★ 方式Aに修正：Requestで project=xx を受け取る
    public function index(Request $request)
    {
        $projectId = $request->input('project');
        $project = Project::findOrFail($projectId);

        $specs = $project->specifications()->with('currentVersion')->paginate(20);
        return view('specifications.index', compact('project','specs'));
    }

    public function show(Request $request, Specification $specification)
    {
        // プロジェクトはクエリから拾うか、リレーションから取得
        $project = Project::findOrFail($request->input('project', $specification->project_id));

        return view('specifications.show', [
            'project' => $project,
            'specification' => $specification,
        ]);
    }
}
