import { defineStore } from "pinia";
import { reactive } from "vue";
import { Laureate, Organisation } from "@/helpers/API";

export const useLaureatesStore = defineStore("laureates", () => {
  const state = reactive({
    laureates: [] as Laureate[],
    page: 1,
    limit: 10,
    totalRecords: 905,
    first: 0,
    years: [] as string[],
    categories: [] as string[],

    organisations: [] as Organisation[],
  });

  return { state };
});
