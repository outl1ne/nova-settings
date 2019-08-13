<template>
  <div v-if="!loading">
    <h1 class="mb-3 text-90 font-normal text-2xl">Settings</h1>
    <div class="card overflow-hidden">
      <form autocomplete="off" v-if="fields.length">
        <div v-for="field in fields" :key="field.name">
          <component
              :is="'form-' + field.component"
              :errors="validationErrors"
              :field="field"
          />
        </div>

        <div class="bg-30 flex items-center px-8 py-4">
          <button type="button" class="btn btn-default btn-primary inline-flex items-center relative mr-3 ml-auto" @click="updateAndContinueEditing">
            <span>Save settings</span>
          </button>
        </div>
      </form>

      <div class="py-3 px-6 border-50" v-else>
        <div class="flex">
          <div class="w-1/4 py-4">
            <h4 class="font-normal text-80">Error</h4>
          </div>
          <div class="w-3/4 py-4">
            <p class="text-90">No settings fields have been defined.</p>
          </div>
        </div>
      </div>
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
