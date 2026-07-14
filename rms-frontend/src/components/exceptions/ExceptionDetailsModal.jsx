function ExceptionDetailsModal({ item, onClose }) {
  if (!item) return null;

  const result = item.reconciliation_result || {};

  return (
    <Modal title="Exception Details" onClose={onClose}>
      <div className="grid gap-4 md:grid-cols-2">
        <Info label="Case Number" value={item.case_number} />
        <Info label="Transaction ID" value={item.txn_id || result.transaction_id} />
        <Info label="Consumer Number" value={result.consumer_number} />
        <Info label="Batch Code" value={result.batch?.batch_code} />
        <Info label="Matching Set" value={result.matching_set?.set_name} />
        <Info label="Exception Code" value={item.exception_code} />
        <Info label="Variance Amount" value={`₹ ${item.variance_amount}`} />
        <Info label="Business Date" value={result.business_date?.substring(0, 10)} />
        <Info label="Priority" value={item.priority} />
        <Info label="Status" value={item.status} />
        <Info label="Assigned To" value={item.assigned_to || "-"} />
        <Info label="Remarks" value={item.remarks} />
      </div>
    </Modal>
  );
}

function Info({ label, value }) {
  return (
    <div>
      <p className="text-sm text-slate-500">{label}</p>
      <p className="mt-1 font-semibold text-slate-800">{value || "-"}</p>
    </div>
  );
}

function Modal({ title, children, onClose }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div className="w-full max-w-3xl rounded-xl bg-white shadow-xl">
        <div className="flex items-center justify-between border-b p-5">
          <h2 className="text-xl font-bold">{title}</h2>
          <button onClick={onClose} className="text-slate-500 hover:text-red-600">
            ✕
          </button>
        </div>

        <div className="p-6">{children}</div>
      </div>
    </div>
  );
}

export default ExceptionDetailsModal;