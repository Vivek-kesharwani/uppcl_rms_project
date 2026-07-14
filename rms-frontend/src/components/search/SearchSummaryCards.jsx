function SearchSummaryCards({ transactions = [] }) {
  const total = transactions.length;

  const matched = transactions.filter(
    (item) => item.result_status === "MATCHED"
  ).length;

  const exceptions = transactions.filter(
    (item) => item.result_status === "EXCEPTION"
  ).length;

  const pending = transactions.filter(
    (item) =>
      item.result_status === "PENDING" ||
      item.result_status === "NEW"
  ).length;

  const cards = [
    {
      title: "Total Transactions",
      value: total,
      color: "text-slate-800",
    },
    {
      title: "Matched",
      value: matched,
      color: "text-green-600",
    },
    {
      title: "Exceptions",
      value: exceptions,
      color: "text-red-600",
    },
    {
      title: "Pending",
      value: pending,
      color: "text-amber-600",
    },
  ];

  return (
    <div className="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
      {cards.map((card) => (
        <div
          key={card.title}
          className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
          <p className="text-sm text-slate-500">
            {card.title}
          </p>

          <h2
            className={`mt-3 text-3xl font-bold ${card.color}`}
          >
            {card.value}
          </h2>
        </div>
      ))}
    </div>
  );
}

export default SearchSummaryCards;