import StatusBadge from "../common/StatusBadge";

function HistoricalBatchTable({

    batches = [],
    onView,

}) {

    return (
        <div className="bg-white rounded-xl shadow border border-slate-200 overflow-hidden">
            <div className="px-6 py-5 border-b">
                <h2 className="text-lg font-semibold">
                    Historical Reconciliation Explorer
                </h2>
                <p className="text-sm text-slate-500 mt-1">
                    Browse previously executed reconciliation batches.
                </p>
            </div>
            <div className="overflow-x-auto">
                <table className="min-w-full text-sm">
                    <thead className="bg-slate-100">
                        <tr>

                            <th className="p-4 text-left">
                                Batch Code
                            </th>

                            <th className="p-4 text-left">
                                Business Date
                            </th>

                            <th className="p-4 text-left">
                                Batch Type
                            </th>

                            <th className="p-4 text-left">
                                Files
                            </th>

                            <th className="p-4 text-left">
                                Records
                            </th>

                            <th className="p-4 text-left">
                                Matched
                            </th>

                            <th className="p-4 text-left">
                                Exceptions
                            </th>

                            <th className="p-4 text-left">
                                Status
                            </th>

                            <th className="p-4 text-left">
                                Action
                            </th>

                        </tr>
                    </thead>

                    <tbody>
                        {
                            batches.map((batch) => (
                                <tr
                                    key={batch.id}
                                    className="border-b hover:bg-slate-50"
                                >

                                    <td className="p-4 font-semibold">
                                        {batch.batch_code}
                                    </td>

                                    <td className="p-4">
                                        {batch.business_date?.substring(0, 10)}
                                    </td>

                                    <td className="p-4">
                                        {batch.batch_type}
                                    </td>

                                    <td className="p-4">
                                        {batch.total_files}
                                    </td>

                                    <td className="p-4">
                                        {batch.total_records}
                                    </td>

                                    <td className="p-4 text-green-700 font-semibold">
                                        {batch.matched_records}
                                    </td>

                                    <td className="p-4 text-red-700 font-semibold">
                                        {batch.exception_records}
                                    </td>

                                    <td className="p-4">
                                        <StatusBadge
                                            status={batch.status}
                                        />
                                    </td>

                                    <td className="p-4">
                                        <button
                                            onClick={() => onView(batch)}
                                            className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                            ))
                        }
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default HistoricalBatchTable;