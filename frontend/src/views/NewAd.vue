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
          <v-col cols="2" class="text-right">

          </v-col>
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
                    <v-textarea
                      counter
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
                <v-img
                  lazy-src="https://picsum.photos/id/11/10/6"
                  src="https://picsum.photos/id/11/500/300"
                ></v-img>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12" class="text-right">
            <v-btn class="mr-4" right color="primary">
              Criar
            </v-btn>
          </v-col>
          <v-col cols="12" v-if="debug">
            <p><b>Similaridade Título e Descrição:</b> 0.000</p>
            <p><b>Similaridade Imagem e Imagem:</b> 0.000</p>
            <p><b>Similaridade Título e Imagem:</b> 0.000</p>
            <p><b>Similaridade Descrição e Imagem:</b> 0.000</p>
            <p><b>Coerência de preço:</b> 0.000</p>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import { VMoney } from 'v-money';

export default {
  directives: { money: VMoney },
  data: () => ({
    debug: false,
    ad: {
      value: 0,
    },
    money: {
      decimal: ',',
      thousands: '.',
      prefix: 'R$ ',
      suffix: '',
      precision: 2,
      masked: false,
    },
  }),
};
</script>
