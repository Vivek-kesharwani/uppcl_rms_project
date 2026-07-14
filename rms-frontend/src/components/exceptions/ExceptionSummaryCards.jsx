function ExceptionSummaryCards({ exceptions = [] }) {
  const total = exceptions.length;
  const open = exceptions.filter((e) => e.status === "OPEN").length;
  const assigned = exceptions.filter((e) => e.status === "ASSIGNED").length;
  const resolved = exceptions.filter((e) => e.status === "RESOLVED").length;
  const closed = exceptions.filter((e) => e.status === "CLOSED").length;
  const highPriority = exceptions.filter((e) =>
    ["HIGH", "CRITICAL"].includes(e.priority)
  ).length;

  const cards = [
    ["Total", total],
    ["Open", open],
    ["Assigned", assigned],
    ["Resolved", resolved],
    ["Closed", closed],
    ["High Priority", highPriority],
  ];

  return (
    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
      {cards.map(([title, value]) => (
        <div
          key={title}
          className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
        >
          <p className="text-sm text-slate-500">{title}</p>
          <p className="mt-2 text-3xl font-bold text-slate-800">{value}</p>
        </div>
      ))}
    </div>
  );
}

export default ExceptionSummaryCards;