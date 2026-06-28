function StatusBadge({ value }) {
  const styles = {
    SUCCESS: "bg-green-100 text-green-700",
    MATCHED: "bg-green-100 text-green-700",
    RESOLVED: "bg-green-100 text-green-700",

    OPEN: "bg-red-100 text-red-700",
    EXCEPTION: "bg-red-100 text-red-700",

    IN_PROGRESS: "bg-yellow-100 text-yellow-700",
    MEDIUM: "bg-yellow-100 text-yellow-700",

    HIGH: "bg-red-100 text-red-700",
    LOW: "bg-blue-100 text-blue-700",
  };

  return (
    <span
      className={`px-3 py-1 rounded-full text-xs font-semibold ${
        styles[value] || "bg-slate-100 text-slate-700"
      }`}
    >
      {value || "-"}
    </span>
  );
}

export default StatusBadge;