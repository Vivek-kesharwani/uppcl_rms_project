import api from "./api";

export const uploadFile = (formData) =>
  api.post("/upload", formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });

export const getUploads = () => api.get("/uploads");
export const getUploadHistory = () => api.get("/upload-history");