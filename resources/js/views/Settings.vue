<template>
  <div v-if="!loading">
    <h1 class="mb-3 text-90 font-normal text-2xl">Settings</h1>
    <div class="card overflow-hidden">
      <form autocomplete="off">
        <div v-for="field in fields" :key="field.name">
          <component
              :is="'form-' + field.component"
              :errors="validationErrors"
              :field="field"
          />
        </div>

        <div class="bg-30 flex items-center px-8 py-4">
          <a class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6">Cancel</a>

          <button type="button" class="btn btn-default btn-primary inline-flex items-center relative mr-3" @click="updateAndContinueEditing">
            <span>Save settings</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Errors } from 'laravel-nova';

export default {
  data() {
    return {
      loading: false,
      fields: [],
      validationErrors: new Errors(),
    };
  },
  async created() {
    this.getFields();
  },
  methods: {
    async getFields() {
      this.loading = true;
      this.fields = [];

      const { data: fields } = await Nova.request()
        .get('/nova-vendor/nova-settings/settings')
        .catch(error => {
          if (error.response.status == 404) {
            this.$router.push({ name: '404' });
            return;
          }
        });

      this.fields = fields;
      this.loading = false;
    },

    async updateAndContinueEditing() {
      try {
        const response = await this.updateRequest();

        this.$toasted.show('Settings successfully updated', {
          type: 'success',
        });

        // Reset the form by refetching the fields
        this.getFields();

        this.validationErrors = new Errors();
      } catch (error) {
        if (error.response.status == 422) {
          this.validationErrors = new Errors(error.response.data.errors);
        }
      }
    },

    updateRequest() {
      return Nova.request().post('/nova-vendor/nova-settings/settings', this.formData);
    },
  },
  computed: {
    formData() {
      return _.tap(new FormData(), formData => {
        _(this.fields).each(field => field.fill(formData));
        formData.append('_method', 'POST');
      });
    },
  },
};
</script>

<style>
</style>
