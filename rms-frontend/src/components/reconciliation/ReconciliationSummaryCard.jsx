import StatusBadge from "../common/StatusBadge";

function ReconciliationSummaryCard({ summary }) {
  if (!summary) return null;

  return (
    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 className="mb-5 text-lg font-semibold text-slate-800">
        Reconciliation Summary
      </h2>

      <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <Info label="Batch Code" value={summary.batch_code} />
        <Info label="Matched" value={summary.matched} />
        <Info label="Exceptions" value={summary.exceptions} />
        <Info label="Total Records" value={summary.total_records} />
        <Info label="Result File" value={summary.result_file?.file_name} />

        <div>
          <p className="text-sm text-slate-500">Status</p>
          <div className="mt-1">
            <StatusBadge status={summary.result_file?.status || "READY"} />
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
      <p className="mt-1 break-all font-semibold text-slate-800">
        {value ?? "-"}
      </p>
    </div>
  );
}

export default ReconciliationSummaryCard;