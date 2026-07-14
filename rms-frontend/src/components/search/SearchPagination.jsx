function SearchPagination({
  currentPage,
  totalPages,
  onPageChange,
}) {
  if (totalPages <= 1) return null;

  return (
    <div className="mt-6 flex items-center justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

      <button
        disabled={currentPage === 1}
        onClick={() => onPageChange(currentPage - 1)}
        className={`rounded-lg px-5 py-2 font-medium ${
          currentPage === 1
            ? "cursor-not-allowed bg-slate-200 text-slate-400"
            : "border hover:bg-slate-100"
        }`}
      >
        Previous
      </button>

      <div className="flex gap-2">

        {Array.from(
          { length: totalPages },
          (_, index) => index + 1
        ).map((page) => (

          <button
            key={page}
            onClick={() => onPageChange(page)}
            className={`h-10 w-10 rounded-lg font-semibold ${
              page === currentPage
                ? "bg-blue-600 text-white"
                : "border hover:bg-slate-100"
            }`}
          >
            {page}
          </button>

        ))}

      </div>

      <button
        disabled={currentPage === totalPages}
        onClick={() => onPageChange(currentPage + 1)}
        className={`rounded-lg px-5 py-2 font-medium ${
          currentPage === totalPages
            ? "cursor-not-allowed bg-slate-200 text-slate-400"
            : "border hover:bg-slate-100"
        }`}
      >
        Next
      </button>

    </div>
  );
}

export default SearchPagination;