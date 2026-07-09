import { useEffect, useState } from "react";
import { getResults } from "../services/dashboardService";

function Search() {
    const [txnId, setTxnId] = useState("");
    const [allResults, setAllResults] = useState([]);
    const [filteredResults, setFilteredResults] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadResults();
    }, []);

    async function loadResults() {
        try {
            const response = await getResults();
            const data = response.data.data || [];
            setAllResults(data);
            setFilteredResults(data);
        } catch (error) {
            console.error("Results API error:", error);
        } finally {
            setLoading(false);
        }
    }

    function searchTransaction() {
        const query = txnId.trim().toUpperCase();

        if (!query) {
            setFilteredResults(allResults);
            return;
        }

        setFilteredResults(
            allResults.filter((item) =>
                item.transaction_id?.toUpperCase().includes(query)
            )
        );
    }

    if (loading) {
        return <p className="text-slate-600">Loading reconciliation results...</p>;
    }

    return (
        <div>
            <h1 className="text-3xl font-bold mb-6">
                Reconciliation Results
            </h1>

            <div className="bg-white rounded-xl shadow p-6 mb-6">
                <div className="flex gap-4">
                    <input
                        type="text"
                        placeholder="Search Transaction ID"
                        value={txnId}
                        onChange={(e) => setTxnId(e.target.value)}
                        className="flex-1 border rounded-lg px-4 py-3"
                    />

                    <button
                        onClick={searchTransaction}
                        className="bg-blue-600 text-white px-8 rounded-lg"
                    >
                        Search
                    </button>
                </div>
            </div>

            <div className="bg-white rounded-xl shadow overflow-x-auto">
                <table className="w-full text-sm">
                    <thead className="bg-slate-100 text-slate-700">
                        <tr>
                            <th className="p-4 text-left">Transaction ID</th>
                            <th className="p-4 text-left">Left Source</th>
                            <th className="p-4 text-left">Right Source</th>
                            <th className="p-4 text-left">Status</th>
                            <th className="p-4 text-left">Exception</th>
                            <th className="p-4 text-left">Remarks</th>
                        </tr>
                    </thead>

                    <tbody>
                        {filteredResults.map((item) => (
                            <tr key={item.id} className="border-t">
                                <td className="p-4 font-semibold">
                                    {item.transaction_id}
                                </td>

                                <td className="p-4">
                                    {item.left_source_id}
                                </td>

                                <td className="p-4">
                                    {item.right_source_id}
                                </td>

                                <td className="p-4">
                                    <span
                                        className={`px-3 py-1 rounded-full text-xs font-semibold ${
                                            item.result_status === "MATCHED"
                                                ? "bg-green-100 text-green-700"
                                                : "bg-red-100 text-red-700"
                                        }`}
                                    >
                                        {item.result_status}
                                    </span>
                                </td>

                                <td className="p-4">
                                    {item.exception_code || "-"}
                                </td>

                                <td className="p-4">
                                    {item.remarks}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                {filteredResults.length === 0 && (
                    <div className="p-8 text-center text-slate-500">
                        No reconciliation results found.
                    </div>
                )}
            </div>
        </div>
    );
}

export default Search;