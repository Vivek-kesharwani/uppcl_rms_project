function AuditDetailsModal({
  log,
  open,
  onClose,
}) {
  if (!open || !log) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

      <div className="w-full max-w-3xl rounded-xl bg-white shadow-xl">

        {/* Header */}

        <div className="flex items-center justify-between border-b p-6">

          <div>

            <h2 className="text-2xl font-bold">
              Audit Log Details
            </h2>

            <p className="text-sm text-slate-500">
              Detailed activity information
            </p>

          </div>

          <button
            onClick={onClose}
            className="rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600"
          >
            Close
          </button>

        </div>

        {/* Body */}

        <div className="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

          <Info
            title="User"
            value={log.user?.name || "System"}
          />

          <Info
            title="Email"
            value={log.user?.email || "-"}
          />

          <Info
            title="Module"
            value={log.module}
          />

          <Info
            title="Action"
            value={log.action}
          />

          <Info
            title="IP Address"
            value={log.ip_address}
          />

          <Info
            title="Created At"
            value={formatDate(log.created_at)}
          />

          <div className="md:col-span-2">

            <p className="text-sm text-slate-500">
              Description
            </p>

            <div className="mt-2 rounded-lg border bg-slate-50 p-4">
              {log.description || "-"}
            </div>

          </div>

        </div>

      </div>

    </div>
  );
}

function Info({ title, value }) {
  return (
    <div>

      <p className="text-sm text-slate-500">
        {title}
      </p>

      <p className="mt-2 font-semibold text-slate-800">
        {value || "-"}
      </p>

    </div>
  );
}

function formatDate(value) {
  if (!value) return "-";

  return new Date(value).toLocaleString();
}

export default AuditDetailsModal;