function ExceptionStatusActions({
  item,
  onAssign,
  onResolve,
  onVerify,
  onClose,
  onReopen,
}) {
  const status = item.status;

  const canAssign = status === "OPEN";
  const canResolve = status === "ASSIGNED";
  const canVerify = status === "RESOLVED";
  const canClose = status === "VERIFIED";
  const canReopen = ["RESOLVED", "VERIFIED", "CLOSED"].includes(status);

  return (
    <div className="flex flex-wrap gap-2">
      <ActionButton
        label="Assign"
        enabled={canAssign}
        onClick={() => onAssign(item)}
      />

      <ActionButton
        label="Resolve"
        enabled={canResolve}
        onClick={() => onResolve(item)}
      />

      <ActionButton
        label="Verify"
        enabled={canVerify}
        onClick={() => onVerify(item)}
      />

      <ActionButton
        label="Close"
        enabled={canClose}
        onClick={() => onClose(item)}
      />

      <ActionButton
        label="Reopen"
        enabled={canReopen}
        danger
        onClick={() => onReopen(item)}
      />
    </div>
  );
}

function ActionButton({ label, enabled, onClick, danger = false }) {
  return (
    <button
      type="button"
      disabled={!enabled}
      onClick={enabled ? onClick : undefined}
      title={enabled ? label : `Not allowed in current status`}
      className={
        enabled
          ? danger
            ? "rounded-lg bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700"
            : "rounded-lg bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700"
          : "cursor-not-allowed rounded-lg bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-400"
      }
    >
      {label}
    </button>
  );
}

export default ExceptionStatusActions;