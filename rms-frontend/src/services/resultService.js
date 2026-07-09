import api from "./api";

export const getResultFiles = () => api.get("/result-files");

export const getResultFile = (id) => api.get(`/result-files/${id}`);

export const downloadResultFile = (id) =>
  api.get(`/result-files/${id}/download`, {
    responseType: "blob",
  });