<template>
  <v-app id="inspire">
    <v-app-bar app>
      <v-toolbar-title>AdChecker 3000 Turbo</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-title class="text-center">
        <router-link to="/new">
          <a href="#">Novo Anúncio</a>
        </router-link>
        <v-switch
          label="Modo Debug"
          dense
          v-model="debug"
          color="red"
          hide-details
        ></v-switch>
      </v-toolbar-title>
    </v-app-bar>
    <v-main class="grey lighten-2">
      <v-container>
        <v-row>
          <v-col md-12>
            <v-card>
              <v-card-title>
                <v-text-field
                  v-model="search"
                  append-icon="fas fa-search"
                  label="Procurar"
                  single-line
                  hide-details
                ></v-text-field>
              </v-card-title>
              <v-data-table
                :headers="headers"
                :items="items"
                :search="search"
                :loading="loading"
              >
                <template v-slot:item.title="{ item }">
                  <v-card>
                    <div class="d-flex flex-no-wrap justify-space-between">
                      <v-avatar class="ma-3" size="125" tile>
                        <v-img
                          :src="`http://localhost:8000${item.image}`"
                        ></v-img>
                      </v-avatar>
                      <div>
                        <v-card-title
                          class="text-h5"
                          v-text="item.title"
                        ></v-card-title>
                        <v-card-subtitle
                          style="text-align: justify"
                          v-text="item.description"
                        ></v-card-subtitle>
                      </div>
                    </div>
                  </v-card>
                </template>
                <template v-slot:item.value="{ item }">
                  <span style="font-size: x-large"
                    >R${{ formatAsPrice(item.value) }}</span
                  >
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
    <v-footer app>
      <span
        ><a href="#" @click="resetAll" v-if="debug">{{ resetText }}</a></span
      >
    </v-footer>
  </v-app>
</template>

<script>
/* eslint-disable quotes */
import axios from "axios";

export default {
  data: () => ({
    debug: false,
    search: "",
    items: [],
    loading: false,
    resetText: "Resetar Anúncios",
  }),
  computed: {
    headers() {
      const items = [
        { text: "Produto", align: "center", value: "title" },
        { text: "Valor", align: "center", value: "value" },
      ];

      if (this.debug) {
        items.push({ text: "Score", align: "center", value: "score" });
      }

      return items;
    },
  },
  methods: {
    formatAsPrice(price) {
      return price
        .toFixed(2)
        .replace(".", ",")
        .replace(/(\d)(?=(\d{3})+(?:,\d+)?$)/g, "$1.");
    },
    getAds() {
      this.loading = true;
      axios
        .get("http://localhost:8000/api/listAds")
        .then((response) => {
          this.items = [];

          if (response.status === 200) {
            this.items = response.data;
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
    resetAll() {
      if (this.loading) {
        return;
      }
      this.resetText = "...";
      this.loading = true;

      axios
        .get("http://localhost:8000/api/resetAll")
        .then((response) => {
          if (response.data.status) {
            this.getAds();
          }
        })
        .finally(() => {
          this.loading = false;
          this.resetText = "Resetar Anúncios";
        });
    },
  },
  mounted() {
    this.getAds();
  },
};
</script>
