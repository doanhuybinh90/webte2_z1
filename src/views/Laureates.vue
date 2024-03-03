<script setup lang="tsx">
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Paginator, { PageState } from "primevue/paginator";
import { onMounted, reactive, ref, watch } from "vue";
import { GetLaureates } from "@/helpers/API";
import { useLaureatesStore } from "@/stores/laureates.store";
import Dropdown from "primevue/dropdown";

const { state } = useLaureatesStore();

onMounted(async () => {
  const data = await GetLaureates(state.page, state.limit, "year", "asc");
  state.laureates = data.data;
  state.totalRecords = data.total;
  state.years = data.uniqueYears;
  state.categories = data.uniqueCategories;
});

const changePage = async (event: PageState) => {
  console.log(event);
  if (event.rows != state.limit) {
    state.limit = event.rows;
    state.page = 1;
    state.first = 0;
  } else {
    state.page = event.page + 1;
    state.first = event.first;
  }

  const data = await GetLaureates(
    state.page,
    state.limit,
    selectedSort.value,
    selectedSortOrder.value,
    selectedYear.value,
    selectedCategory.value
  );
  state.laureates = data.data;
  state.totalRecords = data.total;
};

const sort = reactive({
  name: undefined as "asc" | "desc" | undefined,
  surname: undefined as "asc" | "desc" | undefined,
  year: undefined as "asc" | "desc" | undefined,
  category: undefined as "asc" | "desc" | undefined,
});

const selectedSort = ref("year");
const selectedSortOrder = ref("asc");

function handleFirstSortClick(key: string) {
  Object.keys(sort).forEach((k) => {
    if (k !== key) {
      sort[k as keyof typeof sort] = undefined;
    }
  });
  sort[key as keyof typeof sort] = "asc";
}

watch(
  () => sort,
  (newVal) => {
    Object.keys(newVal).forEach(async (key) => {
      if (newVal[key as keyof typeof sort] !== undefined) {
        selectedSort.value = key;
        selectedSortOrder.value = newVal[key as keyof typeof sort]!;
        const data = await GetLaureates(
          1,
          state.limit,
          key,
          newVal[key as keyof typeof sort]!,
          selectedYear.value,
          selectedCategory.value
        );
        state.laureates = data.data;
        state.totalRecords = data.total;
        state.page = 1;
        state.first = 0;
      }
    });
  },
  { deep: true }
);

const selectedYear = ref(undefined as string | undefined);
const selectedCategory = ref(undefined as string | undefined);

watch(
  () => selectedYear.value,
  () => {
    refetchWithFilter();
  }
);

watch(
  () => selectedCategory.value,
  () => {
    refetchWithFilter();
  }
);

async function refetchWithFilter() {
  const data = await GetLaureates(
    1,
    state.limit,
    selectedSort.value,
    selectedSortOrder.value,
    selectedYear.value,
    selectedCategory.value
  );
  state.laureates = data.data;
  state.totalRecords = data.total;
  state.page = 1;
  state.first = 0;
}
</script>

<template>
  <div class="px-4 sm:px-6">
    <h1 class="text-center">Laureates</h1>
    <div class="flex flex-column md:flex-row gap-0 md:gap-8 mb-4">
      <div class="flex flex-row gap-2 align-items-center">
        <h3>Year:</h3>
        <Dropdown
          placeholder="Select One"
          class="w-full md:w-auto"
          style="min-width: 15rem"
          :showClear="true"
          :options="state.years"
          v-model="selectedYear"
        >
        </Dropdown>
      </div>
      <div class="flex flex-row gap-2 align-items-center">
        <h3>Category:</h3>
        <Dropdown
          placeholder="Select One"
          style="min-width: 15rem"
          class="w-full md:w-auto"
          :showClear="true"
          :options="state.categories"
          v-model="selectedCategory"
        >
        </Dropdown>
      </div>
    </div>
    <DataTable :value="state.laureates">
      <Column field="year" header="Year" v-if="!selectedYear">
        <template #header>
          <span
            class="flex align-items-center justify-content-center cursor-pointer"
            style="order: 2; padding-left: 1rem"
          >
            <i
              class="pi pi-sort-alt"
              v-if="sort.year === undefined"
              @click="handleFirstSortClick('year')"
            ></i>
            <i
              class="pi pi-sort-amount-up-alt"
              v-if="sort.year === 'asc'"
              @click="sort.year = 'desc'"
            ></i>
            <i
              class="pi pi-sort-amount-down-alt"
              v-if="sort.year === 'desc'"
              @click="sort.year = 'asc'"
            ></i>
          </span>
        </template>
      </Column>
      <Column field="name" header="First Name">
        <template #header>
          <span
            class="flex align-items-center justify-content-center cursor-pointer"
            style="order: 2; padding-left: 1rem"
          >
            <i
              class="pi pi-sort-alt"
              v-if="sort.name === undefined"
              @click="handleFirstSortClick('name')"
            ></i>
            <i
              class="pi pi-sort-amount-up-alt"
              v-if="sort.name === 'asc'"
              @click="sort.name = 'desc'"
            ></i>
            <i
              class="pi pi-sort-amount-down-alt"
              v-if="sort.name === 'desc'"
              @click="sort.name = 'asc'"
            ></i>
          </span>
        </template>
        <template #body="slotProps">
          <router-link :to="`/laureates/${slotProps.data.id}`">{{
            slotProps.data.name
          }}</router-link>
        </template>
      </Column>
      <Column field="surname" header="Surname">
        <template #header>
          <span
            class="flex align-items-center justify-content-center cursor-pointer"
            style="order: 2; padding-left: 1rem"
          >
            <i
              class="pi pi-sort-alt"
              v-if="sort.surname === undefined"
              @click="handleFirstSortClick('surname')"
            ></i>
            <i
              class="pi pi-sort-amount-up-alt"
              v-if="sort.surname === 'asc'"
              @click="sort.surname = 'desc'"
            ></i>
            <i
              class="pi pi-sort-amount-down-alt"
              v-if="sort.surname === 'desc'"
              @click="sort.surname = 'asc'"
            ></i>
          </span>
        </template>
      </Column>
      <Column field="category" header="Category" v-if="!selectedCategory">
        <template #header>
          <span
            class="flex align-items-center justify-content-center cursor-pointer"
            style="order: 2; padding-left: 1rem"
          >
            <i
              class="pi pi-sort-alt"
              v-if="sort.category === undefined"
              @click="handleFirstSortClick('category')"
            ></i>
            <i
              class="pi pi-sort-amount-up-alt"
              v-if="sort.category === 'asc'"
              @click="sort.category = 'desc'"
            ></i>
            <i
              class="pi pi-sort-amount-down-alt"
              v-if="sort.category === 'desc'"
              @click="sort.category = 'asc'"
            ></i>
          </span>
        </template>
      </Column>
    </DataTable>
    <Paginator
      :rows="10"
      :rowsPerPageOptions="[10, 20, 50]"
      :totalRecords="state.totalRecords"
      @page="changePage($event)"
      v-model:first="state.first"
      :pageLinkSize="3"
      :pt="{
        root: {
          class: 'px-0',
        },
      }"
    />
  </div>
</template>
