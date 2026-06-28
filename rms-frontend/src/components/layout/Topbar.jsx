import { useNavigate } from "react-router-dom";
import { FaUserCircle } from "react-icons/fa";
import api from "../../services/api";

function Topbar() {
  const navigate = useNavigate();

  const user = JSON.parse(localStorage.getItem("user")) || {};

  const handleLogout = async () => {
    try {
      await api.post("/logout");
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      navigate("/");
    }
  };

  return (
    <div className="h-16 bg-white shadow flex justify-between items-center px-6">
      <h1 className="text-2xl font-semibold text-slate-800">
        UPPCL Reconciliation Management System
      </h1>

      <div className="flex items-center gap-4">
        <FaUserCircle className="text-3xl text-slate-700" />

        <div>
          <div className="font-semibold">{user.name || "User"}</div>
          <div className="text-sm text-gray-500">{user.role || "Role"}</div>
        </div>

        <button
          onClick={handleLogout}
          className="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600"
        >
          Logout
        </button>
      </div>
    </div>
  );
}

export default Topbar;