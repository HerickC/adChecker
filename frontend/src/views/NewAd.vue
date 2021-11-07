<template>
  <v-app id="inspire">
    <v-app-bar app>
      <v-toolbar-title>AdChecker 3000 Turbo</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-title class="text-center">
        <router-link to="/">
          <a href="#">Voltar</a>
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
          <v-col cols="10"></v-col>
          <v-col cols="2" class="text-right"> </v-col>
          <v-col cols="8">
            <v-form ref="form">
              <v-container>
                <v-row>
                  <v-col cols="8">
                    <v-text-field
                      counter
                      v-model="ad.title"
                      v-on:blur="changeTitle"
                      :max="127"
                      label="Título do Anúncio"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="4">
                    <v-text-field
                      v-money="money"
                      v-model="ad.value"
                      label="Valor do Produto"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      counter
                      v-on:blur="changeDescription"
                      v-model="ad.description"
                      label="Descrição"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
          </v-col>
          <v-col cols="4">
            <v-row>
              <v-col cols="12">
                <input
                  v-show="false"
                  ref="inputImage"
                  type="file"
                  accept="image/png,image/jpg,image/jpeg"
                  v-on:change="onChangeImage"
                  required
                />
                <v-img
                  style="cursor: pointer"
                  contain
                  height="200"
                  v-on:click="$refs.inputImage.click()"
                  :src="imageUrl"
                ></v-img>
                <br />
                <v-progress-linear
                  v-if="processingImage"
                  indeterminate
                  color="teal"
                ></v-progress-linear>
                <p v-if="processingImage" class="text-center">
                  Processando Imagem...
                </p>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12" class="text-right">
            <v-btn
              class="mr-4"
              :disabled="!canCreateAd"
              v-on:click="createNewAd"
              right
              color="primary"
            >
              Criar
            </v-btn>
            <br>
            <span v-if="testScores.ov >= 0 && !canCreateAd">
              Seu Anúncio não é bom o suficiente para ser publicado.
            </span>
          </v-col>
          <v-col cols="12" v-if="debug">
            <p><b>ID Temporário:</b> {{ id }}</p>
          </v-col>
          <v-col cols="4" v-if="debug">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">Verificação</th>
                    <th class="text-center">Score</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <span
                        v-bind:class="{
                          ready: canCheckItens.td,
                          notReady: !canCheckItens.td,
                        }"
                      >
                        Similaridade Título e Descrição
                      </span>
                    </td>
                    <td class="text-center">{{ testScores.td.toFixed(3) }}</td>
                    <td class="text-center">
                      <span v-if="processessingTests.td"
                        ><i class="fas fa-spinner fa-spin"></i
                      ></span>
                      <span v-else>
                        <span v-if="checkedTests.td" class="ready"
                          ><i class="fas fa-check"></i
                        ></span>
                        <span v-else class="notReady"
                          ><i class="fas fa-times"></i
                        ></span>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <span
                        v-bind:class="{
                          ready: canCheckItens.it,
                          notReady: !canCheckItens.it,
                        }"
                      >
                        Similaridade Título e Imagem
                      </span>
                    </td>
                    <td class="text-center">{{ testScores.it.toFixed(3) }}</td>
                    <td class="text-center">
                      <span v-if="processessingTests.it"
                        ><i class="fas fa-spinner fa-spin"></i
                      ></span>
                      <span v-else>
                        <span v-if="checkedTests.it" class="ready"
                          ><i class="fas fa-check"></i
                        ></span>
                        <span v-else class="notReady"
                          ><i class="fas fa-times"></i
                        ></span>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <span
                        v-bind:class="{
                          ready: canCheckItens.id,
                          notReady: !canCheckItens.id,
                        }"
                      >
                        Similaridade Descrição e Imagem
                      </span>
                    </td>
                    <td class="text-center">{{ testScores.id.toFixed(3) }}</td>
                    <td class="text-center">
                      <span v-if="processessingTests.id"
                        ><i class="fas fa-spinner fa-spin"></i
                      ></span>
                      <span v-else>
                        <span v-if="checkedTests.id" class="ready"
                          ><i class="fas fa-check"></i
                        ></span>
                        <span v-else class="notReady"
                          ><i class="fas fa-times"></i
                        ></span>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <span
                        v-bind:class="{
                          ready: canCheckItens.ii,
                          notReady: !canCheckItens.ii,
                        }"
                      >
                        Similaridade Imagem e Imagem
                      </span>
                    </td>
                    <td class="text-center">{{ testScores.ii.toFixed(3) }}</td>
                    <td class="text-center">
                      <span v-if="processessingTests.ii"
                        ><i class="fas fa-spinner fa-spin"></i
                      ></span>
                      <span v-else>
                        <span v-if="checkedTests.ii" class="ready"
                          ><i class="fas fa-check"></i
                        ></span>
                        <span v-else class="notReady"
                          ><i class="fas fa-times"></i
                        ></span>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <span
                        v-bind:class="{
                          ready: canCheckItens.ov,
                          notReady: !canCheckItens.ov,
                        }"
                      >
                        Score Final
                      </span>
                    </td>
                    <td class="text-center">{{ testScores.ov.toFixed(3) }}</td>
                    <td class="text-center">
                      <span v-if="processessingTests.ov"
                        ><i class="fas fa-spinner fa-spin"></i
                      ></span>
                      <span v-else>
                        <span v-if="checkedTests.ov" class="ready"
                          ><i class="fas fa-check"></i
                        ></span>
                        <span v-else class="notReady"
                          ><i class="fas fa-times"></i
                        ></span>
                      </span>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-col>
          <v-col cols="1" v-if="debug"> </v-col>
          <v-col cols="7" v-if="debug">
            <p><b>Itens na imagem:</b></p>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-center">Item</th>
                    <th class="text-center">Prob</th>
                    <th class="text-left">Sinônimos</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in imageItems" :key="item.name">
                    <td>{{ item.name }}</td>
                    <td>{{ item.prob.toFixed(2) }}%</td>
                    <td>{{ item.translations }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
/* eslint-disable quotes */
/* eslint-disable prefer-destructuring */
/* eslint-disable no-bitwise */

import axios from "axios";
import { VMoney } from "v-money";
import router from "../router";

export default {
  directives: { money: VMoney },
  data: () => ({
    debug: true,
    image: null,
    imageUrl: `${process.env.VUE_APP_API_HOST}/img/semimagem.png`,
    checkItems: null,
    checkScores: null,
    id: "-",
    ad: {
      id: null,
      title: "",
      description: "",
      value: "",
      score: 0,
    },
    processingImage: false,
    imageItems: [],
    money: {
      decimal: ",",
      thousands: ".",
      prefix: "R$ ",
      suffix: "",
      precision: 2,
      masked: false,
    },
    imageChecked: false,
    canCheckItens: {
      ii: false,
      it: false,
      id: false,
      td: false,
      ov: false,
    },
    checkedTests: {
      ii: false,
      it: false,
      id: false,
      td: false,
      ov: false,
    },
    processessingTests: {
      ii: false,
      it: false,
      id: false,
      td: false,
      ov: false,
    },
    testScores: {
      ii: -1,
      it: -1,
      id: -1,
      td: -1,
      ov: -1,
    },
  }),
  computed: {
    isProcessing() {
      let isProssessingSomething = false;

      isProssessingSomething |= this.processessingTests.ii;
      isProssessingSomething |= this.processessingTests.it;
      isProssessingSomething |= this.processessingTests.id;
      isProssessingSomething |= this.processessingTests.td;
      isProssessingSomething |= this.processessingTests.ov;

      return isProssessingSomething;
    },
    canCalcFinalScore() {
      let canCalc = true;

      canCalc &= this.testScores.ii >= 0;
      canCalc &= this.testScores.it >= 0;
      canCalc &= this.testScores.id >= 0;
      canCalc &= this.testScores.td >= 0;

      return canCalc;
    },
    canCreateAd() {
      return this.testScores.ov > 0.55;
    },
  },
  methods: {
    async getId() {
      this.id = "-";
      await axios
        .get(`${process.env.VUE_APP_API_HOST}/api/uniqueId`)
        .then((response) => {
          this.id = response.data.id;
          this.ad.id = response.data.id;
        });
    },
    async getImageList() {
      const url = `${process.env.VUE_APP_API_HOST}/api/image-list`;
      await axios
        .post(url, {
          id: this.id,
        })
        .then((response) => {
          this.imageItems = response.data.items;

          if (response.data.done) {
            clearInterval(this.checkItems);
            this.processingImage = false;
            this.imageChecked = true;
            this.updateCheckableItems();
          }
        });
    },
    async getScoresItems() {
      const url = `${process.env.VUE_APP_API_HOST}/api/image-list`;
      await axios
        .post(url, {
          id: this.id,
        })
        .then((response) => {
          if (response.data.ii >= 0) {
            this.processessingTests.ii = false;
            this.testScores.ii = response.data.ii;
            this.checkedTests.ii = true;
          }

          if (response.data.it >= 0) {
            this.processessingTests.it = false;
            this.testScores.it = response.data.it;
            this.checkedTests.it = true;
          }

          if (response.data.id >= 0) {
            this.processessingTests.id = false;
            this.testScores.id = response.data.id;
            this.checkedTests.id = true;
          }

          if (response.data.td >= 0) {
            this.processessingTests.td = false;
            this.testScores.td = response.data.td;
            this.checkedTests.td = true;
          }

          if (response.data.ov >= 0) {
            this.processessingTests.ov = false;
            this.testScores.ov = response.data.ov;
            this.checkedTests.ov = true;
          }
        });

      this.updateCheckableItems();

      if (this.canCalcFinalScore && !this.checkedTests.ov) {
        this.checkFinalScore();
      }

      if (!this.isProcessing) {
        clearInterval(this.checkScores);
        this.checkScores = null;
      }
    },
    async onChangeImage(event) {
      if (this.ad.id === null) {
        await this.getId();
      }
      this.image = event.target.files[0];
      this.imageUrl = URL.createObjectURL(this.image);

      this.sendImage();
    },
    async changeTitle() {
      if (this.ad.id === null) {
        await this.getId();
      }
      this.checkedTests.td = false;
      this.checkedTests.it = false;
      this.testScores.td = 0;
      this.testScores.it = 0;
      this.updateCheckableItems();
    },
    async changeDescription() {
      if (this.ad.id === null) {
        await this.getId();
      }
      this.checkedTests.td = false;
      this.checkedTests.id = false;
      this.testScores.td = 0;
      this.testScores.id = 0;
      this.updateCheckableItems();
    },
    updateCheckableItems() {
      this.canCheckItens.ii = this.imageChecked;
      this.canCheckItens.it = this.ad.title.length > 0 && this.imageChecked;
      this.canCheckItens.id = this.ad.description.length > 0 && this.imageChecked;
      this.canCheckItens.td = this.ad.title.length > 0 && this.ad.description.length > 0;
      this.canCheckItens.ov = this.canCalcFinalScore;
      this.processItems();
    },
    sendImage() {
      const url = `${process.env.VUE_APP_API_HOST}/api/upload-image`;
      const formData = new FormData();

      formData.append("file", this.image);
      formData.append("id", this.id);

      return axios
        .post(url, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then(() => {
          this.imageChecked = false;
          this.processingImage = true;
          this.checkItems = setInterval(this.getImageList, 3000);
        });
    },
    processItems() {
      if (this.canCheckItens.ii && !this.checkedTests.ii) {
        this.checkImageImage();
      }
      if (this.canCheckItens.it && !this.checkedTests.it) {
        this.checkImageTitle();
      }
      if (this.canCheckItens.id && !this.checkedTests.id) {
        this.checkImageDescription();
      }
      if (this.canCheckItens.td && !this.checkedTests.td) {
        this.checkTitleDescription();
      }

      if (this.checkScores === null) {
        this.checkScores = setInterval(this.getScoresItems, 3000);
      }
    },
    checkImageImage() {
      this.processessingTests.ii = true;
      this.checkedTests.ii = true;

      const url = `${process.env.VUE_APP_API_HOST}/api/check-image-image`;

      axios.post(url, this.ad);
    },
    checkImageTitle() {
      this.processessingTests.it = true;
      this.checkedTests.it = true;

      const url = `${process.env.VUE_APP_API_HOST}/api/check-image-title`;

      axios.post(url, this.ad);
    },
    checkImageDescription() {
      this.processessingTests.id = true;
      this.checkedTests.id = true;

      const url = `${process.env.VUE_APP_API_HOST}/api/check-image-description`;

      axios.post(url, this.ad);
    },
    checkTitleDescription() {
      this.processessingTests.td = true;
      this.checkedTests.td = true;

      const url = `${process.env.VUE_APP_API_HOST}/api/check-title-description`;

      axios.post(url, this.ad);
    },
    checkFinalScore() {
      this.processessingTests.ov = true;
      this.checkedTests.ov = true;

      const url = `${process.env.VUE_APP_API_HOST}/api/check-final-score`;

      axios.post(url, this.ad);
    },
    createNewAd() {
      const url = `${process.env.VUE_APP_API_HOST}/api/new`;

      axios.post(url, this.ad).then(() => {
        router.push("/");
      });
    },
  },
  mounted() {},
};
</script>
<style>
.notReady {
  color: red;
}
.ready {
  color: forestgreen;
}
</style>
