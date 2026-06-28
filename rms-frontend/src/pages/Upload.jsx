import { useState } from "react";
import api from "../services/api";

function Upload() {
  const [files, setFiles] = useState({
    agency: null,
    billing: null,
    bank: null,
  });

  const [message, setMessage] = useState("");

  const handleFileChange = (type, file) => {
    setFiles((prev) => ({
      ...prev,
      [type]: file,
    }));
  };

  const uploadFile = async (type) => {
    if (!files[type]) {
      setMessage(`Please select ${type} CSV file`);
      return;
    }

    const formData = new FormData();
    formData.append("file", files[type]);

    try {
      const response = await api.post(`/${type}/upload`, formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      setMessage(`${type.toUpperCase()} upload successful. Rows: ${response.data.inserted_rows}`);
    } 
    
    catch (error) {
        console.error("Upload Error:", error);

        if (error.response) {
            console.log(error.response.data);
            setMessage(
                error.response.data.message ||
                `${type.toUpperCase()} upload failed`
            );
        } else {
            setMessage(error.message);
        }
    }
  };

  return (
    <div>
      <h1 className="text-2xl font-bold text-slate-800 mb-6">Upload Files</h1>

      {message && (
        <div className="mb-5 bg-blue-100 text-blue-700 px-4 py-3 rounded-lg">
          {message}
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {["agency", "billing", "bank"].map((type) => (
          <div key={type} className="bg-white rounded-xl shadow p-6 border">
            <h2 className="text-lg font-semibold mb-4 capitalize">
              {type} CSV
            </h2>

            <input
              type="file"
              accept=".csv"
              onChange={(e) => handleFileChange(type, e.target.files[0])}
              className="mb-4"
            />

            <button
              onClick={() => uploadFile(type)}
              className="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700"
            >
              Upload {type}
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}

export default Upload;