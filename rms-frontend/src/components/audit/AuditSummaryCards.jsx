function AuditSummaryCards({ summary = {} }) {
  const cards = [
    {
      title: "Total Activities",
      value: summary.total_logs ?? 0,
      textClass: "text-slate-800",
    },
    {
      title: "Today's Activities",
      value: summary.today_activities ?? 0,
      textClass: "text-blue-600",
    },
    {
      title: "File Upload Actions",
      value: summary.upload_actions ?? 0,
      textClass: "text-cyan-600",
    },
    {
      title: "Reconciliation Actions",
      value: summary.reconciliation_actions ?? 0,
      textClass: "text-violet-600",
    },
    {
      title: "Exception Actions",
      value: summary.exception_actions ?? 0,
      textClass: "text-red-600",
    },
    {
      title: "Result File Actions",
      value: summary.result_file_actions ?? 0,
      textClass: "text-green-600",
    },
    {
      title: "Authentication Actions",
      value: summary.authentication_actions ?? 0,
      textClass: "text-amber-600",
    },
  ];

  return (
    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      {cards.map((card) => (
        <div
          key={card.title}
          className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
        >
          <p className="text-sm font-medium text-slate-500">
            {card.title}
          </p>

          <p className={`mt-3 text-3xl font-bold ${card.textClass}`}>
            {card.value}
          </p>
        </div>
      ))}
    </div>
  );
}

export default AuditSummaryCards;