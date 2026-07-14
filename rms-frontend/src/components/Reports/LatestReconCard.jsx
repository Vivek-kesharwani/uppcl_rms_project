import StatusBadge from "../common/StatusBadge";

function LatestReconCard({ batch, resultFile }) {
  if (!batch) {
    return (
      <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 className="text-lg font-semibold text-slate-800">
          Latest Reconciliation Snapshot
        </h2>
        <p className="mt-2 text-sm text-slate-500">
          No reconciliation batch is available yet.
        </p>
      </div>
    );
  }

  return (
    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <div className="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
          <h2 className="text-lg font-semibold text-slate-800">
            Latest Reconciliation Snapshot
          </h2>
          <p className="text-sm text-slate-500">
            Most recent reconciliation batch and linked result file.
          </p>
        </div>

        <StatusBadge status={batch.status} />
      </div>

      <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <Info label="Batch Code" value={batch.batch_code} />
        <Info label="Business Date" value={batch.business_date?.substring(0, 10)} />
        <Info label="Batch Type" value={batch.batch_type} />
        <Info label="Run Mode" value={batch.run_mode} />

        <Info label="Total Files" value={batch.total_files} />
        <Info label="Ready Files" value={batch.ready_files} />
        <Info label="Started At" value={formatDateTime(batch.started_at)} />
        <Info label="Completed At" value={formatDateTime(batch.completed_at)} />

        <Info
          label="Result File"
          value={resultFile?.file_name || "No result file linked"}
          wide
        />
      </div>
    </div>
  );
}

function Info({ label, value, wide = false }) {
  return (
    <div className={wide ? "md:col-span-2 xl:col-span-4" : ""}>
      <p className="text-sm text-slate-500">{label}</p>
      <p className="mt-1 break-all font-semibold text-slate-800">
        {value ?? "-"}
      </p>
    </div>
  );
}

function formatDateTime(value) {
  if (!value) return "-";
  return value.replace("T", " ").substring(0, 19);
}

export default LatestReconCard;