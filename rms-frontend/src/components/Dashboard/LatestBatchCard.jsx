import StatusBadge from "../common/StatusBadge";

function LatestBatchCard({ batch }) {
  if (!batch) {
    return (
      <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        No latest batch available.
      </div>
    );
  }

  return (
    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 className="mb-5 text-lg font-semibold text-slate-800">
        Latest Reconciliation Batch
      </h2>

      <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <Info label="Batch Code" value={batch.batch_code} />
        <Info label="Business Date" value={batch.business_date?.substring(0, 10)} />
        <Info label="Run Mode" value={batch.run_mode} />
        <Info label="Total Files" value={batch.total_files} />
        <Info label="Matched Records" value={batch.matched_records} />
        <Info label="Exception Records" value={batch.exception_records} />

        <div>
          <p className="text-sm text-slate-500">Status</p>
          <div className="mt-1">
            <StatusBadge status={batch.status} />
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

export default LatestBatchCard;