function TransactionDetailsModal({ transaction, onClose }) {
  if (!transaction) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

      <div className="w-full max-w-4xl rounded-xl bg-white shadow-xl">

        {/* Header */}

        <div className="flex items-center justify-between border-b p-6">

          <div>

            <h2 className="text-2xl font-bold">
              Transaction Details
            </h2>

            <p className="text-sm text-slate-500">
              Enterprise Transaction Information
            </p>

          </div>

          <button
            onClick={onClose}
            className="rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600"
          >
            Close
          </button>

        </div>

        {/* Body */}

        <div className="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

          <Info
            title="Transaction ID"
            value={transaction.transaction_id}
          />

          <Info
            title="Consumer Number"
            value={transaction.consumer_number}
          />

          <Info
            title="Account Number"
            value={transaction.account_number}
          />

          <Info
            title="Amount"
            value={`₹ ${transaction.amount}`}
          />

          <Info
            title="Business Date"
            value={transaction.business_date}
          />

          <Info
            title="Matching Set"
            value={
              transaction.matching_set?.set_name || "-"
            }
          />

          <Info
            title="Batch Code"
            value={
              transaction.batch?.batch_code || "-"
            }
          />

          <Info
            title="Result Status"
            value={transaction.result_status}
          />

          <Info
            title="Exception Code"
            value={transaction.exception_code || "-"}
          />

          <Info
            title="Variance Amount"
            value={`₹ ${transaction.variance_amount || 0}`}
          />

          <div className="md:col-span-2">

            <p className="text-sm text-slate-500">
              Remarks
            </p>

            <div className="mt-2 rounded-lg border bg-slate-50 p-4">
              {transaction.remarks || "No remarks available."}
            </div>

          </div>

        </div>

      </div>

    </div>
  );
}

function Info({ title, value }) {
  return (
    <div>

      <p className="text-sm text-slate-500">
        {title}
      </p>

      <p className="mt-2 font-semibold text-slate-800">
        {value || "-"}
      </p>

    </div>
  );
}

export default TransactionDetailsModal;