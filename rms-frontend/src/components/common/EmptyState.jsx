function EmptyState({ title = "No data found", description = "There is nothing to show right now." }) {
  return (
    <div className="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
      <h3 className="text-lg font-semibold text-slate-800">{title}</h3>
      <p className="mt-1 text-sm text-slate-500">{description}</p>
    </div>
  );
}

export default EmptyState;