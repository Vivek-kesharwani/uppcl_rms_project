import { Search } from "lucide-react";

function SearchBox({ value, onChange, placeholder = "Search..." }) {
  return (
    <div className="relative">
      <Search className="absolute left-3 top-2.5 text-slate-400" size={18} />
      <input
        value={value}
        onChange={(e) => onChange(e.target.value)}
        placeholder={placeholder}
        className="w-full rounded-lg border border-slate-300 bg-white py-2 pl-10 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
      />
    </div>
  );
}

export default SearchBox;