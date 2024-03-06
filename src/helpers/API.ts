const BASE_URL = "https://node33.webte.fei.stuba.sk/z1/api";
import axios from "axios";

export interface Laureate {
  id: string;
  category: string;
  country: string;
  name: string;
  surname: string;
  year: string;
}

export interface LaureateDetails {
  id: string;
  category: string;
  country: string;
  name: string;
  surname: string;
  organisation: string;
  year: string;
  sex: string;
  birth?: string;
  death?: string;
  language_sk?: string;
  language_en?: string;
  genre_sk?: string;
  genre_en?: string;
  contribution_sk: string;
  contribution_en: string;
}

export interface AddLaureateBody {
  category: string;
  country: string;
  name: string;
  surname: string;
  organisation: string;
  year: string;
  sex: string;
  birth: string;
  death?: string;
  language_sk?: string;
  language_en?: string;
  genre_sk?: string;
  genre_en?: string;
  contribution_sk: string;
  contribution_en: string;
}

export interface Organisation {
  id: string;
  category: string;
  country: string;
  organisation: string;
  year: string;
}

export interface User {
  username: string;
  email: string;
  password: string;
}

export const GetLaureates = async (
  page: number,
  limit: number,
  sortKey: string,
  sortMethod: string,
  filterYearValue?: string,
  filterCategoryValue?: string
) => {
  const response = await axios.get(`${BASE_URL}/laureates`, {
    params: {
      page,
      limit,
      sortKey,
      sortMethod,
      filterYearValue,
      filterCategoryValue,
    },
  });
  return response.data as {
    data: Laureate[];
    total: number;
    uniqueYears: string[];
    uniqueCategories: string[];
  };
};

export const GetOrganisations = async (
  page: number,
  limit: number,
  sortKey: string,
  sortMethod: string,
  filterYearValue?: string,
  filterCategoryValue?: string
) => {
  const response = await axios.get(`${BASE_URL}/organisations`, {
    params: {
      page,
      limit,
      sortKey,
      sortMethod,
      filterYearValue,
      filterCategoryValue,
    },
  });
  return response.data as {
    data: Organisation[];
    total: number;
    uniqueYears: string[];
    uniqueCategories: string[];
  };
};

export const GetTwoFactorAuthQR = async () => {
  const response = await axios.get(`${BASE_URL}/users/2fa`, {
    headers: {
      "Cache-Control": "no-cache",
      Pragma: "no-cache",
      Expires: "0",
    },
  });
  return response.data as { qrCodeUrl: string };
};

export const VerifyTwoFactorAuth = async (code: string) => {
  const response = await axios.post(`${BASE_URL}/users/2fa/`, {
    oneCode: code,
  });
  return response.data as { verified: boolean };
};

export const ValidateEmail = async (email: string) => {
  const response = await axios.post(`${BASE_URL}/users/validate/`, {
    email,
  });
  return response.data as { exists: boolean };
};

export const ValidateLogin = async (login: string, password: string) => {
  const response = await axios.post(`${BASE_URL}/users/validateLogin/`, {
    login,
    password,
  });
  return response.data as { isCorrect: boolean; message?: string };
};

export const ValidateOtp = async (login: string, otpCode: string) => {
  const response = await axios.post(`${BASE_URL}/users/validateOTP/`, {
    login,
    otpCode,
  });
  return response.data as {
    isCorrect: boolean;
    message?: string;
    username?: string;
  };
};

export const RegisterUser = async (body: User) => {
  const response = await axios.post(`${BASE_URL}/users/register/`, {
    ...body,
  });
  return response.data as { success: boolean };
};

export const GetLaureateById = async (id: string) => {
  const response = await axios.get(`${BASE_URL}/laureates`, { params: { id } });
  return response.data as { data: LaureateDetails[] };
};

export const IsUserAuthorized = async () => {
  const response = await axios.get(`${BASE_URL}/users/isAuthorized/`);
  return response.data as {
    isAuthorized: boolean;
    username: string;
    email: string;
  };
};

export const AddLaureate = async (body: AddLaureateBody) => {
  const response = await axios.post(`${BASE_URL}/laureates/`, {
    ...body,
  });
  return response.data as { success: boolean; isOrganisation: boolean };
};

export const DeleteLaureate = async (id: string) => {
  const response = await axios.post(`${BASE_URL}/laureates/delete/`, {
    id,
  });
  return response.data as { success: boolean };
};

export const GoogleLoginAuth = async (email: string, username: string) => {
  const response = await axios.post(`${BASE_URL}/users/googleLogin/`, {
    username,
    email,
  });
  return response.data as { success: boolean };
};

export const Logout = async () => {
  const response = await axios.get(`${BASE_URL}/users/logout/`);
  return response.data as { success: boolean };
};
