<template>
  <loading-view :loading="loading">
    <form v-if="panels" @submit.prevent="update" autocomplete="off">
      <form-panel
        v-for="panel in panelsWithFields"
        :panel="panel"
        :name="panel.name"
        :key="panel.name"
        :fields="panel.fields"
        :resource-name="'nova-settings'"
        :resource-id="'settings'"
        mode="form"
        class="mb-6"
        :validation-errors="validationErrors"
      />

      <!-- Update Button -->
      <div class="flex items-center">
        <progress-button
          type="submit"
          class="ml-auto"
          @click.native="update"
          :disabled="isUpdating"
          :processing="isUpdating"
        >
          {{ __('novaSettings.saveButtonText') }}
        </progress-button>
      </div>
    </form>

    <div class="py-3 px-6 border-50" v-else>
      <div class="flex">
        <div class="w-1/4 py-4">
          <h4 class="font-normal text-80">Error</h4>
        </div>
        <div class="w-3/4 py-4">
          <p class="text-90">{{ __('novaSettings.noSettingsFieldsText') }}</p>
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
        .get('/nova-vendor/nova-settings/settings?editing=true&editMode=update')
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

        if (response && response.data && response.data.reload === true) {
          location.reload();
          return;
        }

        this.$toasted.show(this.__('novaSettings.settingsSuccessToast'), {
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

<style></style>
