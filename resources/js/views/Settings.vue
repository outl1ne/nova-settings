<template>
    <loading-view :loading="loading">
        <form v-if="panels" @submit.prevent="update" autocomplete="off">
            <form-panel
                v-for="panel in panelsWithFields"
                :panel="panel"
                :name="panel.name"
                :key="panel.name"
                :fields="panel.fields"
                mode="form"
                class="mb-6"
                :validation-errors="validationErrors"
            />

            <!-- Update Button -->
            <div class="flex items-center">
              <progress-button
                class="ml-auto"
                @click.native="update"
                :disabled="isUpdating"
                :processing="isUpdating"
              >
                {{ __('Save settings') }}
              </progress-button>
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
    </loading-view>
</template>

<script>
import { Errors } from 'laravel-nova';

export default {
  data() {
    return {
      loading: false,
      isUpdating: false,
      fields: [],
      panels: [],
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

      const {
        data: { fields, panels },
      } = await Nova.request()
        .get('/nova-vendor/nova-settings/settings')
        .catch(error => {
          if (error.response.status == 404) {
            this.$router.push({ name: '404' });
            return;
          }
        });

      this.fields = fields;
      this.panels = panels;
      this.loading = false;
    },

    async update() {
      try {
        this.isUpdating = true;
        const response = await this.updateRequest();

        this.$toasted.show('Settings successfully updated', {
          type: 'success',
        });

        // Reset the form by refetching the fields
        await this.getFields();

        this.isUpdating = false;
        this.validationErrors = new Errors();
      } catch (error) {
        console.error(error);

        this.isUpdating = false;

        if (error && error.response && error.response.status == 422) {
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

    panelsWithFields() {
      return _.map(this.panels, panel => {
        return {
          name: panel.name,
          fields: _.filter(this.fields, field => field.panel == panel.name),
        };
      });
    },
  },
};
</script>

<style>
</style>
