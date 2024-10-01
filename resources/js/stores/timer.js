import { defineStore } from "pinia";
import apiService from "../service/apiService";
import { toast } from "../service/toastWrapper";

export const useTimerStore = defineStore("timer", {
    state: () => ({
        timers: null,
        pageLoader: true,
        actionLoader: false,
    }),
    actions: {
        async getTimers(id) {
            this.pageLoader = true;
            try {
                const res = await apiService.get(`/api/players/${id}/timers`);
                this.timers = res?.data?.data;
            } catch (error) {
                console.log(error);
            } finally {
                this.pageLoader = false;
            }
        },
        async postTimers(payload, id) {
            this.actionLoader = true;
            const clonePayload = { ...payload };
            if (clonePayload?.id) {
                delete clonePayload.id;
            }
            try {
                const res = payload?.id
                    ? await apiService.patch(
                          `/api/players/${id}/timers/${payload.id}`,
                          clonePayload
                      )
                    : await apiService.post(
                          `/api/players/${id}/timers`,
                          clonePayload
                      );
                const data = {
                    ...res.data,
                    reminders: JSON.parse(res.data.reminders),
                };
                if (payload?.id) {
                    const index = this.timers.findIndex(
                        (timer) => timer.id === payload.id
                    );
                    if (index !== -1) {
                        this.timers[index] = {
                            ...this.timers[index],
                            ...data,
                        };
                    }
                } else {
                    this.timers = [...this.timers, data];
                }
                toast.success(
                    `Timer has been ${
                        !payload.id ? "created" : "updated"
                    } successfully`
                );
                return Promise.resolve();
            } catch (errors) {
                const { response } = errors;
                toast.error(response?.data?.message || "Something went wrong");
                throw errors;
            } finally {
                this.actionLoader = false;
            }
        },
        async deleteTimer(playerId, id) {
            this.actionLoader = true;
            try {
                await apiService.delete(
                    `/api/players/${playerId}/timers/${id}`
                );
                this.timers = this.timers.filter((x) => x.id !== id);
                toast.success(`Timer has been deleted successfully`);
            } catch (errors) {
                const { response } = errors;
                toast.error(response?.data?.message || "Something went wrong");
            } finally {
                this.actionLoader = false;
            }
        },
    },
});
