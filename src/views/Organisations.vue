<script setup lang="tsx">
import { onMounted, reactive, ref, watch } from "vue";

import { DeleteLaureate, GetOrganisations } from "@/helpers/API";
import { useLaureatesStore } from "@/stores/laureates.store";
import { useUserStore } from "@/stores/user.store";

import Paginator, { PageState } from "primevue/paginator";
import InlineMessage from "primevue/inlinemessage";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import DataTable from "primevue/datatable";
import Dropdown from "primevue/dropdown";
import Column from "primevue/column";
import Toast from "primevue/toast";
import Card from "primevue/card";

const confirm = useConfirm();
const toast = useToast();
const { state } = useLaureatesStore();

onMounted(async () => {
  const data = await GetOrganisations(state.page, state.limit, "year", "asc");
  state.organisations = data.data;
  state.totalRecords = data.total;
  state.years = data.uniqueYears;
  state.categories = data.uniqueCategories;
});

const changePage = async (event: PageState) => {
  if (event.rows != state.limit) {
    state.limit = event.rows;
    state.page = 1;
    state.first = 0;
  } else {
    state.page = event.page + 1;
    state.first = event.first;
  }

  const data = await GetOrganisations(
    state.page,
    state.limit,
    selectedSort.value,
    selectedSortOrder.value,
    selectedYear.value,
    selectedCategory.value
  );
  state.organisations = data.data;
  state.totalRecords = data.total;
};

const sort = reactive({
  organisation: undefined as "asc" | "desc" | undefined,
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
        const data = await GetOrganisations(
          1,
          state.limit,
          key,
          newVal[key as keyof typeof sort]!,
          selectedYear.value,
          selectedCategory.value
        );
        state.organisations = data.data;
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
  const data = await GetOrganisations(
    1,
    state.limit,
    selectedSort.value,
    selectedSortOrder.value,
    selectedYear.value,
    selectedCategory.value
  );
  state.organisations = data.data;
  state.totalRecords = data.total;
  state.page = 1;
  state.first = 0;
}

const { user } = useUserStore();
function deleteLaureate(id: string) {
  confirm.require({
    message: "Are you sure you want to delete this laureate?",
    accept: async () => {
      const data = await DeleteLaureate(id);
      if (data.success) {
        await refetchWithFilter();
        toast.add({
          severity: "success",
          summary: "Success",
          detail: "Laureate deleted successfully",
          life: 3000,
        });
      }
    },
  });
}
</script>

<template>
  <Toast></Toast>
  <ConfirmDialog></ConfirmDialog>
  <div class="px-4 sm:px-6">
    <h1 class="text-center">Organisations</h1>
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
    <Card>
      <template #content>
        <DataTable :value="state.organisations">
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
          <Column field="organisation" header="Organisation name">
            <template #header>
              <span
                class="flex align-items-center justify-content-center cursor-pointer"
                style="order: 2; padding-left: 1rem"
              >
                <i
                  class="pi pi-sort-alt"
                  v-if="sort.organisation === undefined"
                  @click="handleFirstSortClick('organisation')"
                ></i>
                <i
                  class="pi pi-sort-amount-up-alt"
                  v-if="sort.organisation === 'asc'"
                  @click="sort.organisation = 'desc'"
                ></i>
                <i
                  class="pi pi-sort-amount-down-alt"
                  v-if="sort.organisation === 'desc'"
                  @click="sort.organisation = 'asc'"
                ></i>
              </span>
            </template>
            <template #body="slotProps">
              <router-link :to="`/laureates/${slotProps.data.id}`">{{
                slotProps.data.organisation
              }}</router-link>
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
          <Column v-if="user.isLogged">
            <template #body="slotProps">
              <InlineMessage
                class="cursor-pointer"
                @click="deleteLaureate(slotProps.data.id)"
              >
                Delete
              </InlineMessage>
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
      </template>
    </Card>
  </div>
</template>
