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
                    <v-textarea counter label="Descrição"></v-textarea>
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
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12" class="text-right">
            <v-btn class="mr-4" v-on:click="getImageList" right color="primary"> Criar </v-btn>
          </v-col>
          <v-col cols="6" v-if="debug">
            <p><b>Similaridade Título e Descrição:</b> 0.000</p>
            <p><b>Similaridade Imagem e Imagem:</b> 0.000</p>
            <p><b>Similaridade Título e Imagem:</b> 0.000</p>
            <p><b>Similaridade Descrição e Imagem:</b> 0.000</p>
            <p><b>Coerência de preço:</b> 0.000</p>
          </v-col>
          <v-col cols="6" v-if="debug">
            <p><b>ID Temporário:</b> {{ id }}</p>
            <p><b>Itens na imagem:</b> {{ imageItems }}</p>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
/* eslint-disable quotes */
/* eslint-disable prefer-destructuring */
import axios from "axios";
import { VMoney } from "v-money";

export default {
  directives: { money: VMoney },
  data: () => ({
    debug: true,
    ad: {
      value: 0,
    },
    image: null,
    imageUrl: `${process.env.VUE_APP_API_HOST}/img/semimagem.png`,
    newAd: {
      title: "",
      description: "",
      value: "",
    },
    checkItems: null,
    id: "-",
    imageItems: [],
    money: {
      decimal: ",",
      thousands: ".",
      prefix: "R$ ",
      suffix: "",
      precision: 2,
      masked: false,
    },
  }),
  methods: {
    async getId() {
      this.id = "-";
      await axios
        .get(`${process.env.VUE_APP_API_HOST}/api/uniqueId`)
        .then((response) => {
          this.id = response.data.id;
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

          if (this.imageItems.length > 0) {
            clearInterval(this.checkItems);
          }
        });
    },
    async onChangeImage(event) {
      await this.getId();
      this.image = event.target.files[0];
      this.imageUrl = URL.createObjectURL(this.image);

      this.sendImage();
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
          this.checkItems = setInterval(this.getImageList, 3000);
        });
    },
  },
  mounted() {},
};
</script>
