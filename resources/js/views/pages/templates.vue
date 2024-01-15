<template>
  <div id="page-template">
    <h2 class="text-center mb-5">Ready Made Templates</h2>
    <b-container fluid class="template-list">
      <b-row>
        <b-col
          lg="4"
          md="6"
          v-for="(tmp, idx) in templates"
          :key="`template-${idx}`"
        >
          <template-card :info="tmp" @onChoose="handleChoose(tmp)" />
        </b-col>
      </b-row>
      <h3 class="mt-5 text-center text-danger" v-if="templates.length === 0 && loaded">
        Template coming soon!
      </h3>
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
$base-color: #1998e4;
#page-template {
  padding: 30px;
  height: 100%;
  background-color: white;
  border-radius: 30px;
  display: flex;
  flex-direction: column;
  .template-list {
    flex: 1;
  }
}
</style>

<script>
import axios from 'axios';
import TemplateCard from '@/views/components/template-card';
export default {
  name: 'templates',
  components: {
    TemplateCard
  },
  data() {
    return {
      templates: [],
      loaded: false,
    };
  },
  mounted() {
    const loader = this.$loading.show();
    axios
      .get('/api/templates')
      .then(res => {
        if (res && res.status === 200) {
          this.templates = res.data.templates;
        }
      })
      .catch(err => {})
      .finally(() => {
        loader && loader.hide();
        this.loaded = true;
      });
  },
  methods: {
    handleChoose(info) {
      const loader = this.$loading.show();
      axios
        .get(`/api/clone-template?id=${info.id}`)
        .then(res => {
          loader && loader.hide();
          if (res && res.status === 200) {
            // redirect to step1 of vidpop
            this.$router.push({
              path: `vidgen/${res.data.vidpop.id}/${res.data.first}/edit/overlay`
            });
          } else {
            // not found
            this.$func.showTextMessage('Error', 'Template not found!', 'error');
          }
        })
        .catch(err => {
          // console.log(err);
          this.$func.showTextMessage('Error', 'Template not found!', 'error');
          loader && loader.hide();
        });
    }
  }
};
</script>
