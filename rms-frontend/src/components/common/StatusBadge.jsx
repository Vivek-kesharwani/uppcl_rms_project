function StatusBadge({ status }) {
  const value = status || "UNKNOWN";

  const styles = {
    RECEIVED: "bg-cyan-100 text-cyan-700",
    STAGED: "bg-yellow-100 text-yellow-700",
    COMPLETED: "bg-green-100 text-green-700",
    RECONCILED: "bg-blue-100 text-blue-700",
    NOT_USED: "bg-slate-100 text-slate-700",
    IN_BATCH: "bg-purple-100 text-purple-700",
    READY: "bg-green-100 text-green-700",
    OPEN: "bg-red-100 text-red-700",
    ASSIGNED: "bg-orange-100 text-orange-700",
    RESOLVED: "bg-blue-100 text-blue-700",
    VERIFIED: "bg-indigo-100 text-indigo-700",
    CLOSED: "bg-green-100 text-green-700",
    FAILED: "bg-red-100 text-red-700",
    MATCHED: "bg-green-100 text-green-700",
    EXCEPTION: "bg-red-100 text-red-700",
  };

  return (
    <span className={`rounded-full px-3 py-1 text-xs font-semibold ${styles[value] || "bg-slate-100 text-slate-700"}`}>
      {value}
    </span>
  );
}

export default StatusBadge;