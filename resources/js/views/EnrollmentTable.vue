<template>
    <v-app>
        <v-container>
            <v-row justify="center">
                <v-col cols="11">
                    <v-card>
                        <v-card-title>
                            Enrollments Report

                        </v-card-title>
                        <v-card-text>
                            <v-combobox
                                v-model="statusItems"
                                :items="statusSelect"
                                :item-text="status"
                                chips
                                hide-selected
                                label="Search or select status"
                                multiple
                                prepend-icon="mdi-filter-variant"
                                solo
                                @input="changeStatus()"
                            >
                                <template v-slot:item="{item}">
                                    {{ status[item - 1] }}
                                </template>
                                <template v-slot:selection="{ attrs, item, select, selected }">
                                    <v-chip
                                        v-bind="attrs"
                                        :input-value="selected"
                                        close
                                        @click:close="remove(item)"
                                    >
                                        <strong v-if="statusSelect.includes(item)">{{ status[item - 1] }}</strong>
                                        <strong v-else>{{ item }}</strong>
                                    </v-chip>
                                </template>
                            </v-combobox>
                            <v-data-table
                                :headers="headers"
                                :items="enrollments"
                                :server-items-length="meta.total"
                                :page.sync="meta.current_page"
                                hide-default-footer
                                :loading="loading"
                                class="elevation-1"
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                @update:sort-by="getEnrolls"
                                @update:sort-desc="getEnrolls"
                            >
                                <template v-slot:item.students.name="{ item }">
                                    <v-btn depressed text class="btnContent" @click="getDetail(item.students.id, item.students.name, 'students')">
                                        {{ item.students.name }}
                                    </v-btn>
                                </template>
                                <template v-slot:item.courses.name="{ item }">
                                    <v-btn depressed text class="btnContent" @click="getDetail(item.courses.id, item.courses.name, 'courses')">
                                        {{ item.courses.name }}
                                    </v-btn>
                                </template>
                                <template v-slot:item.status="{ item }">
                                    <v-chip :color="getColor(item.status)" dark>{{ status[item.status - 1] }}</v-chip>
                                </template>
                            </v-data-table>
                            <div class="text-center pt-2">
                                <v-pagination
                                    v-model="meta.current_page"
                                    :length="meta.last_page"
                                    :total-visible="7"
                                    @input="onPageChange"
                                ></v-pagination>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-app>
</template>

<script>

export default {
    created() {
        if (!$.isEmptyObject(this.$route.query)) {
            this.meta.current_page = parseInt(this.$route.query.page)
            this.meta.per_page = parseInt(this.$route.query.size)
        } else {
            this.meta.current_page = 1
        }
        this.getEnrolls()
    },

    data() {
        return {
            enrollments: [],
            status: ['Not started', 'In progress', 'Completed'],
            statusSelect: ['1', '2', '3'],
            statusItems: ['1', '2', '3'],
            headers: [
                {
                    text: 'ID',
                    align: 'start',
                    sortable: false,
                    value: 'id',
                },
                { text: 'Student Name', value: 'students.name',sortable: false, },
                { text: 'Course Name', value: 'courses.name',sortable: false, },
                { text: 'Enroll Date', value: 'enroll_date' },
                { text: 'Complete Date', value: 'complete_date' },
                { text: 'Status', value: 'status' },
            ],
            meta: { current_page: 1 },
            loading: true,
            search: null,
            detail: null,
            sortDesc: [true],
            sortBy: ['enroll_date']
        }
    },

    methods: {
        async getEnrolls () {
            this.loading = true;
            this.getSearch();
            var url = null;
            var status = [...this.statusItems];
            /**
             * prepare the url based on search criteria
             */
            if (this.detail) {
                url = '/api/' + this.detail.type + '/' + this.detail.id;
                /**
                 * remove search from status array
                 */
                status.splice(status.indexOf(this.search), 1)
            } else if (this.search) {

                url = '/api/search/' + this.search;
                /**
                 * remove search from status array
                 */
                status.splice(status.indexOf(this.search), 1)
            } else {
                url = '/api';
            }

            url += '?page=' + this.meta.current_page;

            if (status.length > 0 && status.length < 3)
                url += '&status=' + status.join();

            if (this.sortBy[0]) {
                url += '&sort=' + this.sortBy[0];

                if(this.sortDesc[0])
                    url += '&desc=1';
            }

            const { data } = await axios.get(url);
            this.enrollments = data.data;
            this.meta = data.meta;
            this.loading = false;
        },

        async getDetail(id, name, type) {
            this.loading = true;
            this.statusItems = [...this.statusSelect];
            this.detail = {type: type, id: id};
            this.search = type.charAt(0) + ':' + name;
            this.statusItems.push(this.search);
            this.meta.current_page = 1;

            await this.getEnrolls();
        },

        onPageChange(page) {
            this.meta.current_page = page
            this.getEnrolls();
        },

        getColor(status) {
            if (status == 1) return 'red'
            else if (status == 2) return 'orange'
            else return 'green'
        },

        remove(item) {
            /**
             * remove the item and clone
             */
            this.statusItems.splice(this.statusItems.indexOf(item), 1)
            this.statusItems = [...this.statusItems]

            this.getEnrolls();
        },

        changeStatus() {
            this.getEnrolls();
        },

        getSearch() {
            var isSearch = false;
            var isDetail = false;
            this.statusItems.forEach((ele) => {
                if (!this.statusSelect.includes(ele)) {
                    if (ele.includes('s:') || ele.includes('c:')) {
                        isDetail = true;
                    } else {
                        isSearch = true;
                        this.search = ele;
                    }
                }
            });

            if (!isSearch && !isDetail) {
                this.search = null;
                this.detail = null;
            } else if (!isDetail)
                this.detail = null;
        },
    },

}
</script>
