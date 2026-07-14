import StatusBadge from "../common/StatusBadge";

function BatchDetailsCard({ summary }) {
  if (!summary) return null;

  return (
    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 className="mb-5 text-lg font-semibold text-slate-800">
        Batch Details
      </h2>

      <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <Info label="Batch Code" value={summary.batch_code} />
        <Info label="Batch ID" value={summary.batch_id} />
        <Info label="Matching Set ID" value={summary.matching_set_id} />
        <Info label="Left File ID" value={summary.left_file_id} />
        <Info label="Right File ID" value={summary.right_file_id} />
        <Info label="Total Files" value={summary.total_files} />

        <div>
          <p className="text-sm text-slate-500">Status</p>
          <div className="mt-1">
            <StatusBadge status="COMPLETED" />
          </div>
        </div>
      </div>
    </div>
  );
}

function Info({ label, value }) {
  return (
    <div>
      <p className="text-sm text-slate-500">{label}</p>
      <p className="mt-1 font-semibold text-slate-800">{value ?? "-"}</p>
    </div>
  );
}

export default BatchDetailsCard;