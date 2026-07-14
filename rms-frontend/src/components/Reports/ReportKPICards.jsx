function ReportKPICards({ batch }) {
  const total = Number(batch?.total_records || 0);
  const matched = Number(batch?.matched_records || 0);
  const exceptions = Number(batch?.exception_records || 0);

  const matchPercent =
    total > 0 ? ((matched / total) * 100).toFixed(2) + "%" : "0.00%";

  const exceptionPercent =
    total > 0 ? ((exceptions / total) * 100).toFixed(2) + "%" : "0.00%";

  const processingTime = getProcessingTime(batch?.started_at, batch?.completed_at);

  const cards = [
    {
      title: "Total Records",
      value: total,
      subtitle: "Records processed in selected batch",
    },
    {
      title: "Matched Records",
      value: matched,
      subtitle: "Successfully reconciled transactions",
    },
    {
      title: "Exception Records",
      value: exceptions,
      subtitle: "Transactions requiring review",
    },
    {
      title: "Match %",
      value: matchPercent,
      subtitle: "Matched records over total records",
    },
    {
      title: "Exception %",
      value: exceptionPercent,
      subtitle: "Exception records over total records",
    },
    {
      title: "Processing Time",
      value: processingTime,
      subtitle: "Batch execution duration",
    },
  ];

  return (
    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
      {cards.map((card) => (
        <div
          key={card.title}
          className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
        >
          <p className="text-sm font-medium text-slate-500">{card.title}</p>

          <p className="mt-2 text-3xl font-bold text-slate-800">
            {card.value}
          </p>

          <p className="mt-2 text-xs text-slate-400">{card.subtitle}</p>
        </div>
      ))}
    </div>
  );
}

function getProcessingTime(start, end) {
  if (!start || !end) return "-";

  const startTime = new Date(start).getTime();
  const endTime = new Date(end).getTime();

  if (Number.isNaN(startTime) || Number.isNaN(endTime)) return "-";

  const diffSeconds = Math.max(0, Math.round((endTime - startTime) / 1000));

  if (diffSeconds < 60) return `${diffSeconds}s`;

  const minutes = Math.floor(diffSeconds / 60);
  const seconds = diffSeconds % 60;

  return `${minutes}m ${seconds}s`;
}

export default ReportKPICards;