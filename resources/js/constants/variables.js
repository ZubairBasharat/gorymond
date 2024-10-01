export const BASE_URL = import.meta.env.VITE_APP_BASE_URL;
export const SITE_TOKEN_VAR = "goraymond__token";
export const SetValueToStorage = (name, value) => {
    localStorage.setItem(name, JSON.stringify(value));
};

export const getStorageValue = (name) => {
    return localStorage.getItem(name)
        ? JSON.parse(localStorage.getItem(name) || {})
        : null;
};

export const RemoveValueFromStorage = (name) => {
    localStorage.removeItem(name);
};
