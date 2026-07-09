import { useState } from "react";
import { FaUpload } from "react-icons/fa";
import { uploadFile } from "../services/uploadService";
import PageHeader from "../components/common/PageHeader";

function Upload() {

    const [file, setFile] = useState(null);

    const [uploading, setUploading] = useState(false);

    const [response, setResponse] = useState(null);

    const [error, setError] = useState("");

    const handleUpload = async () => {

        if (!file) {

            setError("Please choose a CSV file.");

            return;

        }

        const formData = new FormData();

        formData.append("file", file);

        setUploading(true);

        setError("");

        setResponse(null);

        try {

            const res = await uploadFile(formData);

            setResponse(res.data.data);

        } catch (err) {

            setError(

                err.response?.data?.message ||

                "Upload failed."

            );

        }

        setUploading(false);

    };

    return (

        <div>

            <PageHeader

                title="Enterprise File Upload"

                subtitle="Upload reconciliation source files. Source detection is automatic from filename."

            />

            <div className="bg-white rounded-xl shadow p-8">

                <div className="border-2 border-dashed rounded-xl p-10 text-center">

                    <FaUpload

                        className="mx-auto text-5xl text-blue-600"

                    />

                    <h2 className="text-xl font-semibold mt-4">

                        Upload CSV File

                    </h2>

                    <p className="text-gray-500 mt-2">

                        Supported Format

                    </p>

                    <p className="font-mono mt-1">

                        SourceName_daily_DDMMYYYY.csv

                    </p>

                    <p className="font-mono">

                        SourceName_monthly_MMYYYY.csv

                    </p>

                    <input

                        type="file"

                        accept=".csv"

                        className="mt-8"

                        onChange={(e)=>setFile(e.target.files[0])}

                    />

                    <button

                        onClick={handleUpload}

                        disabled={uploading}

                        className="mt-6 bg-blue-600 text-white px-8 py-3 rounded-lg"

                    >

                        {

                            uploading

                                ?

                                "Uploading..."

                                :

                                "Upload"

                        }

                    </button>

                </div>

            </div>

            {

                error &&

                <div className="mt-6 bg-red-100 border border-red-300 rounded-lg p-5">

                    {error}

                </div>

            }

            {

                response &&

                <div className="mt-6 bg-white rounded-xl shadow p-6">

                    <h2 className="text-xl font-bold mb-5">

                        Upload Summary

                    </h2>

                    <div className="grid grid-cols-2 gap-6">

                        <Info title="Source"

                              value={response.source_name} />

                        <Info title="Category"

                              value={response.source_type} />

                        <Info title="File"

                              value={response.file_name} />

                        <Info title="Period"

                              value={response.file_type} />

                        <Info title="Business Date"

                              value={response.business_date ?? "-"} />

                        <Info title="Business Month"

                              value={response.business_month ?? "-"} />

                        <Info title="Status"

                              value={response.status} />

                        <Info title="Checksum"

                              value={response.checksum.substring(0,18)+"..."} />

                    </div>

                </div>

            }

        </div>

    );

}

function Info({title,value}){

    return(

        <div>

            <p className="text-gray-500">

                {title}

            </p>

            <p className="font-semibold">

                {value}

            </p>

        </div>

    );

}

export default Upload;