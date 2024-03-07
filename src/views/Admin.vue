<script setup lang="tsx">
import { computed, ref, watch } from "vue";
import Card from "primevue/card";
import SelectButton from "primevue/selectbutton";
import Textarea from "primevue/textarea";
import InputNumber from "primevue/inputnumber";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import InlineMessage from "primevue/inlinemessage";
import InputMask from "primevue/inputmask";
import Button from "primevue/button";

import { router } from "@/router";

import { useToast } from "primevue/usetoast";
const toast = useToast();

import { AddLaureateBody, AddLaureate } from "@/helpers/API";

const laureate = ref<AddLaureateBody>({
  name: "",
  surname: "",
  organisation: "",
  birth: 1900 as any as string,
  death: "",
  country: "",
  contribution_sk: "",
  contribution_en: "",
  sex: "",
  year: 1900 as any as string,
  category: "",
  language_sk: "",
  language_en: "",
  genre_sk: "",
  genre_en: "",
});

const selectOptions = ref(["Person", "Organisation"]);
const selectedValue = ref("Person");
const isPerson = computed(() => selectedValue.value === "Person");

const categories = ref([
  "Physics",
  "Chemistry",
  "Medicine",
  "Peace",
  "Literature",
]);

const genders = ref(["Male", "Female"]);

const formIncomplete = ref(false);

async function submitForm() {
  formIncomplete.value = false;

  if (isPerson.value && !laureate.value.name && !laureate.value.surname)
    formIncomplete.value = true;
  if (!isPerson.value && !laureate.value.organisation)
    formIncomplete.value = true;
  if (!laureate.value.birth) formIncomplete.value = true;
  if (!laureate.value.contribution_sk || !laureate.value.contribution_en)
    formIncomplete.value = true;
  if (!laureate.value.country) formIncomplete.value = true;
  if (!laureate.value.year) formIncomplete.value = true;
  if (!laureate.value.category) formIncomplete.value = true;
  if (laureate.value.category === "Literature") {
    if (!laureate.value.language_sk || !laureate.value.language_en)
      formIncomplete.value = true;
    if (!laureate.value.genre_sk || !laureate.value.genre_en)
      formIncomplete.value = true;
  }

  if (formIncomplete.value) return;

  laureate.value.birth = laureate.value.birth.toString();
  laureate.value.year = laureate.value.year.toString();

  await AddLaureate(laureate.value)
    .then((data) => {
      laureate.value = {
        name: "",
        surname: "",
        organisation: "",
        birth: 1900 as any as string,
        death: "",
        country: "",
        contribution_sk: "",
        contribution_en: "",
        category: "",
        sex: "",
        year: 1900 as any as string,
        language_sk: "",
        language_en: "",
        genre_sk: "",
        genre_en: "",
      };
      toast.add({
        severity: "success",
        summary: "Success",
        detail: "Laureate added successfully",
        life: 3000,
      });
      router.push(data.isOrganisation ? "/z1/organisations" : "/z1/laureates");
    })
    .catch((error) => {
      toast.add({
        severity: "error",
        summary: "Laureate is not added",
        detail: error.message,
        life: 3000,
      });
    });
}

watch(
  () => isPerson.value,
  (value) => {
    if (value) {
      laureate.value.organisation = "";
    } else {
      laureate.value.name = "";
      laureate.value.surname = "";
    }
  }
);
</script>

<template>
  <div class="px-2 md:px-8">
    <h1 class="text-center">Add <span class="gradient-text">Laureate</span></h1>
    <Card>
      <template #content>
        <div class="flex flex-column gap-5">
          <SelectButton
            class="text-center"
            v-model="selectedValue"
            :options="selectOptions"
          />
          <div class="flex flex-column gap-3">
            <div class="flex gap-2 flex-column md:flex-row" v-if="isPerson">
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="name">
                  First name <span style="color: var(--red-500)">*</span>
                </label>
                <InputText
                  id="name"
                  v-model="laureate.name"
                  class="w-full"
                  :maxlength="100"
                />
              </div>
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="lastname">
                  Lastname <span style="color: var(--red-500)">*</span>
                </label>
                <InputText
                  class="w-full"
                  id="lastname"
                  v-model="laureate.surname"
                  :maxlength="150"
                />
              </div>
            </div>
            <div v-else>
              <div class="flex flex-column gap-2 w-12">
                <label for="organisation">
                  Organisation name <span style="color: var(--red-500)">*</span>
                </label>
                <InputText
                  class="w-full"
                  id="organisation"
                  v-model="laureate.organisation"
                  :maxlength="255"
                />
              </div>
            </div>
            <div class="flex gap-2 flex-column md:flex-row">
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="birth">
                  {{ isPerson ? "Birth year" : "Creation date" }}
                  <span style="color: var(--red-500)">*</span>
                </label>
                <InputNumber
                  id="birth"
                  v-model="laureate.birth as any as number"
                  class="w-full"
                  :min="1900"
                  :useGrouping="false"
                  :max="new Date().getFullYear()"
                />
              </div>
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="death">
                  {{ isPerson ? "Death year" : "Company collapse date" }}
                </label>
                <InputMask
                  class="w-full"
                  id="death"
                  v-model="laureate.death"
                  mask="9999"
                />
              </div>
            </div>
            <div class="flex gap-2 flex-column md:flex-row">
              <div
                class="flex flex-column gap-2 w-12"
                :class="[isPerson ? 'md:w-3' : 'md:w-4']"
              >
                <label for="country">
                  Country <span style="color: var(--red-500)">*</span>
                </label>
                <InputText
                  id="country"
                  v-model="laureate.country"
                  class="w-full"
                  :maxlength="255"
                />
              </div>
              <div
                class="flex flex-column gap-2 w-12"
                :class="[isPerson ? 'md:w-3' : 'md:w-4']"
                v-if="isPerson"
              >
                <label for="sex">
                  Sex <span style="color: var(--red-500)">*</span>
                </label>
                <Dropdown
                  class="w-full"
                  id="sex"
                  :options="genders"
                  v-model="laureate.sex"
                />
              </div>
              <div
                class="flex flex-column gap-2 w-12"
                :class="[isPerson ? 'md:w-3' : 'md:w-4']"
              >
                <label for="year">
                  Year <span style="color: var(--red-500)">*</span>
                </label>
                <InputNumber
                  id="year"
                  v-model="laureate.year as any as number"
                  :useGrouping="false"
                  :max="new Date().getFullYear()"
                  inputClass="w-full"
                />
              </div>
              <div
                class="flex flex-column gap-2 w-12"
                :class="[isPerson ? 'md:w-3' : 'md:w-4']"
              >
                <label for="category">
                  Category <span style="color: var(--red-500)">*</span>
                </label>
                <Dropdown
                  class="w-full"
                  id="category"
                  :options="categories"
                  v-model="laureate.category"
                  required
                />
              </div>
            </div>
            <div class="flex gap-2 flex-column md:flex-row">
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="contribution_sk">
                  Contribution (in Slovak)
                  <span style="color: var(--red-500)">*</span>
                </label>
                <Textarea
                  id="contribution_sk"
                  v-model="laureate.contribution_sk"
                  required
                  class="w-full"
                  style="resize: none"
                  rows="5"
                  maxlength="347"
                />
              </div>
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="contribution_en">
                  Contribution (in English)
                  <span style="color: var(--red-500)">*</span>
                </label>
                <Textarea
                  class="w-full"
                  id="contribution_en"
                  v-model="laureate.contribution_en"
                  required
                  style="resize: none"
                  rows="5"
                  maxlength="374"
                />
              </div>
            </div>

            <div
              class="flex gap-2 flex-column md:flex-row"
              v-if="laureate.category == 'Literature'"
            >
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="language_sk">
                  Language (in Slovak)
                  <span style="color: var(--red-500)">*</span>
                </label>
                <InputText
                  id="language_sk"
                  v-model="laureate.language_sk"
                  class="w-full"
                  maxlength="255"
                />
              </div>
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="language_en">
                  Language (in English)
                  <span style="color: var(--red-500)">*</span>
                </label>
                <InputText
                  class="w-full"
                  id="language_en"
                  v-model="laureate.language_en"
                  maxlength="255"
                />
              </div>
            </div>
            <div
              class="flex gap-2 flex-column md:flex-row"
              v-if="laureate.category == 'Literature'"
            >
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="genre_sk">
                  Genre (in Slovak)
                  <span style="color: var(--red-500)">*</span>
                </label>
                <InputText
                  id="genre_sk"
                  v-model="laureate.genre_sk"
                  class="w-full"
                  maxlength="255"
                />
              </div>
              <div class="flex flex-column gap-2 w-12 md:w-6">
                <label for="genre_en">
                  Genre (in English)
                  <span style="color: var(--red-500)">*</span>
                </label>
                <InputText
                  class="w-full"
                  id="genre_en"
                  v-model="laureate.genre_en"
                  maxlength="255"
                />
              </div>
            </div>
          </div>
          <InlineMessage v-if="formIncomplete">
            Please, fill all the required fields
          </InlineMessage>
          <Button @click="submitForm" label="Add" class="w-3 mx-auto" />
        </div>
      </template>
    </Card>
  </div>
</template>

<style scoped>
.gradient-text {
  background: linear-gradient(
    74deg,
    rgb(66, 133, 244) 0px,
    rgb(155, 114, 203) 9%,
    rgb(217, 101, 112) 20%,
    rgb(217, 101, 112) 24%,
    rgb(155, 114, 203) 35%,
    rgb(66, 133, 244) 44%,
    rgb(155, 114, 203) 50%,
    rgb(217, 101, 112) 56%,
    rgb(217, 101, 112) 60%,
    rgb(155, 114, 203) 71%,
    rgb(66, 133, 244) 80%,
    rgb(155, 114, 203) 89%,
    rgb(217, 101, 112) 100%
  );
  color: #fff; /* White text for better contrast */
  padding: 5px; /* Add some padding for better readability */
  background-clip: text;
  animation: textShine 10s linear infinite;
  background-size: 500% auto;

  -webkit-text-fill-color: transparent;
}

@keyframes textShine {
  0% {
    background-position: 0% 50%;
  }
  100% {
    background-position: 100% 50%;
  }
}
</style>
