function EmptyState({ title = "No data found", message }) {
  return (
    <div className="bg-white rounded-xl shadow border border-slate-200 p-10 text-center">
      <div className="text-4xl mb-3">📄</div>
      <h2 className="text-xl font-semibold text-slate-800">{title}</h2>
      {message && <p className="text-slate-500 mt-2">{message}</p>}
    </div>
  );
}

export default EmptyState;