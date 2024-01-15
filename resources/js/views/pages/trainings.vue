<template>
  <div id="page-training">
    <div class="page-header">
      <div class="d-flex mb-md-0 mb-3 header-title">
        <img src="/images/icons/training.svg" />
        <h5 class="ml-2">Training Videos</h5>
      </div>

      <div class="search-wrapper ml-md-3 ml--">
        <fa-icon :icon="['fas', 'search']" class="search-icon" />
        <input
          type="text"
          class="form-control"
          placeholder="Search for training"
          v-model="search"
        />
      </div>
    </div>
    <b-container fluid class="h-100">
      <b-row>
        <b-col
          v-for="(item, index) in trainings"
          :key="`training-card-${index}`"
          class="mt-md-5 mt-4"
          md="4"
        >
          <training-card :info="item" />
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
$base-color: #1998e4;
#page-training {
  padding: 30px;
  height: 100%;
  background-color: white;
  border-radius: 30px;
  @media (max-width: 768px) {
    padding: 30px 0px;
    border-radius: 0px;
  }
  .page-header {
    display: flex;
    align-items: center;
    @media (max-width: 768px) {
      flex-direction: column;
    }
    .header-title img {
      width: 25px;
    }
  }
  .search-wrapper {
    max-width: 350px;
    width: 100%;
    position: relative;
    .search-icon {
      position: absolute;
      left: 20px;
      top: 12px;
      color: #a1a1a1;
    }
    input {
      padding-left: 45px;
      border-radius: 30px;
      background-color: #eff7fe;
    }
  }
}
</style>

<script>
import axios from 'axios';
import TrainingCard from '@/views/components/training-card';
export default {
  name: 'trainings',
  components: {
    TrainingCard
  },
  data() {
    return {
      trainings: [],
      search: ''
    };
  },
  mounted() {
    axios.get('/api/trainings').then(res => {
      if (res && res.status === 200) {
        this.trainings = res.data;
      }
    });
  },
  methods: {}
};
</script>
