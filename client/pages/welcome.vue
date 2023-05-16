<template>
  <div>
    <div class="text-center">
      <div class="mb-4">
        <p>Finding Restaurants : {{ keyword }}</p>
        <input v-model="keyword" placeholder="Bang sue" @keyup="getRestaurant(2500)" />
      </div>
    </div>
    <div class="text-left">
      <div v-if="restaurants" class="list row px-5 mb-4">
        <div v-for="(rs, inx) in restaurants" :key="inx" class="col-lg-4 col-md-6 col-sm-12 mb-4 ">
          <div class="p-3 border border-success restaurant-box">
            <label><a :href="`https://www.google.com/maps/place/?q=place_id:${rs.place_id}`" target="_blank">{{ rs.name }}</a> <sub>( {{ rs.rating }}<span class="fa fa-star checked"></span>)</sub></label>
            <div class="text-wrap address-box">{{ rs.formatted_address }}</div>
          </div>
        </div>
      </div>
      <div v-else class="list row px-5">
        {{ answer }}
      </div>
      <div v-show="tokenNextPage" class="text-center mx-auto col-lg-4 col-md-6 col-sm-12 mb-4">
        <button class="btn btn-warning" @click="loadMoreResult()">load more</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  layout: 'simple',

  data: () => ({
    title: process.env.appName,
    keyword: '',
    timer: null,
    restaurants: [],
    answer: '',
    tokenNextPage: '',
  }),

  head () {
    return { title: this.$t('home') }
  },
  mounted() {
    this.getRestaurant(0);
  },
  methods: {
    getRestaurant(delay) {
      // function for search results
      this.answer = 'Thinking...'

      clearTimeout(this.timer)

      this.timer = setTimeout(() => {
        try {
          const res = this.$axios.$post(`${process.env.apiUrl}/get-restaurants-list`, {
            keyword: this.keyword || 'Bang sue',
            nextPage: false
          })
          res.then(obj => {
            this.restaurants = obj.results
            this.tokenNextPage = obj.next_page_token
          })

        } catch (error) {
          this.answer = 'Error! Could not reach the API. ' + error
        }
      }, delay)
    },
    loadMoreResult() {
      // function for load more results
      try {
          const res = this.$axios.$post(`${process.env.apiUrl}/get-restaurants-list`, {
            keyword: this.keyword || 'Bang sue',
            nextPage: this.tokenNextPage
          })
          res.then(obj => {
            this.restaurants = this.restaurants.concat(obj.results);
            this.tokenNextPage = obj.next_page_token
          })
        } catch (error) {
          this.answer = 'Error! Could not reach the API. ' + error
        }
    }
  }
}
</script>

<style scoped>
.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

.title {
  font-size: 85px;
}

.list {
  max-height: 80vh;
  overflow-y: auto;
}
.checked {
  color: orange;
}
.address-box{
  height: 50px;
  overflow-y: hidden;
}
.restaurant-box{
  border-radius: 1.025rem !important;
}
.nuxt {
  color: #00c48d;
}
</style>
