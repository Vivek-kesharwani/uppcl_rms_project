import { NavLink } from "react-router-dom";
import {
  FaTachometerAlt,
  FaUpload,
  FaHistory,
  FaExclamationTriangle,
  FaSearch,
  FaChartBar,
} from "react-icons/fa";

function Sidebar() {
  const menuItems = [
    { name: "Dashboard", path: "/dashboard", icon: <FaTachometerAlt /> },
    { name: "Upload Files", path: "/upload", icon: <FaUpload /> },
    { name: "Upload History", path: "/upload-history", icon: <FaHistory /> },
    { name: "Exceptions", path: "/exceptions", icon: <FaExclamationTriangle /> },
    { name: "Transaction Search", path: "/search", icon: <FaSearch /> },
    { name: "Reports", path: "/reports", icon: <FaChartBar /> },
  ];

  return (
    <div className="w-64 bg-slate-900 text-white min-h-screen">
      <div className="text-2xl font-bold text-center py-6 border-b border-slate-700">
        RMS System
      </div>

      <div className="mt-4">
        {menuItems.map((item) => (
          <NavLink
            key={item.path}
            to={item.path}
            className={({ isActive }) =>
              `flex items-center gap-3 px-6 py-3 hover:bg-slate-700 ${
                isActive ? "bg-blue-600" : ""
              }`
            }
          >
            {item.icon}
            {item.name}
          </NavLink>
        ))}
      </div>
    </div>
  );
}

export default Sidebar;