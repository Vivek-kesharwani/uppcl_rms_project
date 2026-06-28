import { useState } from "react";
import api from "../services/api";

function Search() {

    const [txnId, setTxnId] = useState("");
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);

    const searchTransaction = async () => {

        if (!txnId.trim()) return;

        setLoading(true);

        try {

            const response = await api.get(
                `/transactions/search?txn_id=${txnId}`
            );

            setResults(response.data.data);

        } catch (error) {

            console.error(error);

            setResults([]);

        }

        setLoading(false);
    };

    return (

        <div>

            <h1 className="text-3xl font-bold mb-6">
                Transaction Investigation
            </h1>

            <div className="bg-white rounded-xl shadow p-6">

                <div className="flex gap-4">

                    <input

                        type="text"

                        placeholder="Enter Transaction ID"

                        value={txnId}

                        onChange={(e)=>setTxnId(e.target.value)}

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

            {loading && (

                <div className="mt-8">

                    Searching...

                </div>

            )}

            {!loading && results.length>0 && (

                <div className="mt-8">

                    <div className="bg-white rounded-xl shadow p-6">

                        <h2 className="text-xl font-bold mb-6">

                            Search Result

                        </h2>

                        {results.map((item)=>(

                            <div
                                key={item.recon_id}
                                className="grid grid-cols-2 lg:grid-cols-3 gap-6"
                            >

                                <div>
                                    <p className="text-gray-500">Transaction ID</p>
                                    <p className="font-semibold">{item.txn_id}</p>
                                </div>

                                <div>
                                    <p className="text-gray-500">Account</p>
                                    <p className="font-semibold">{item.account_no}</p>
                                </div>

                                <div>
                                    <p className="text-gray-500">DISCOM</p>
                                    <p className="font-semibold">{item.discom}</p>
                                </div>

                                <div>
                                    <p className="text-gray-500">Agency Amount</p>
                                    <p className="font-semibold">
                                        ₹ {item.agency_amount}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-gray-500">Billing Amount</p>
                                    <p className="font-semibold">
                                        ₹ {item.billing_amount}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-gray-500">Bank Amount</p>
                                    <p className="font-semibold">
                                        ₹ {item.settled_amount}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-gray-500">
                                        Reconciliation
                                    </p>
                                    <p className="font-semibold">
                                        {item.recon_status}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-gray-500">
                                        Exception
                                    </p>
                                    <p className="font-semibold">
                                        {item.exception_code ?? "-"}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-gray-500">
                                        Severity
                                    </p>
                                    <p className="font-semibold">
                                        {item.severity ?? "-"}
                                    </p>
                                </div>

                            </div>

                        ))}

                    </div>

                </div>

            )}

        </div>

    );

}

export default Search;