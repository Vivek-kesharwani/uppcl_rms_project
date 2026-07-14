import StatusBadge from "../common/StatusBadge";

function SearchResultsTable({
  results = [],
  onView,
}) {
  if (!results.length) {
    return (
      <div className="bg-white rounded-xl shadow border border-slate-200 p-10 text-center text-slate-500">
        No Transactions Found
      </div>
    );
  }

  return (
    <div className="bg-white rounded-xl shadow border border-slate-200 overflow-x-auto">

      <table className="w-full text-sm">

        <thead className="bg-slate-100">

          <tr>

            <th className="p-4 text-left">
              Transaction ID
            </th>

            <th className="p-4 text-left">
              Consumer
            </th>

            <th className="p-4 text-left">
              Account
            </th>

            <th className="p-4 text-left">
              Amount
            </th>

            <th className="p-4 text-left">
              Business Date
            </th>

            <th className="p-4 text-left">
              Matching Set
            </th>

            <th className="p-4 text-left">
              Result
            </th>

            <th className="p-4 text-left">
              Exception
            </th>

            <th className="p-4 text-left">
              Batch
            </th>

            <th className="p-4 text-left">
              Action
            </th>

          </tr>

        </thead>

        <tbody>

          {results.map((item) => (

            <tr
              key={item.id}
              className="border-b hover:bg-slate-50"
            >

              <td className="p-4 font-semibold">
                {item.transaction_id}
              </td>

              <td className="p-4">
                {item.consumer_number}
              </td>

              <td className="p-4">
                {item.account_number}
              </td>

              <td className="p-4">
                ₹ {item.amount}
              </td>

              <td className="p-4">
                {item.business_date?.substring(0,10)}
              </td>

              <td className="p-4">
                {item.matching_set?.set_name || "-"}
              </td>

              <td className="p-4">
                <StatusBadge
                  status={item.result_status}
                />
              </td>

              <td className="p-4">

                {item.exception_code
                  ? (
                    <span className="text-red-600 font-semibold">
                      {item.exception_code}
                    </span>
                  )
                  : "-"}

              </td>

              <td className="p-4">
                {item.batch?.batch_code || "-"}
              </td>

              <td className="p-4">

                <button
                  onClick={() => onView(item)}
                  className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
                >
                  View
                </button>

              </td>

            </tr>

          ))}

        </tbody>

      </table>

    </div>
  );
}

export default SearchResultsTable;