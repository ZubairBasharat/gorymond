import { defineStore } from "pinia";
import apiService from "../service/apiService";
import { toast } from "../service/toastWrapper";

import { usePlayerStore } from "./player";
import moment from "moment";
export const useEventsStore = defineStore("events", {
    state: () => ({
        events: [],
        recurring: [],
        singleEvent: { repeat_options: "None" },
        actionLoader: false,
        initialLoader: false,
        calendarDate: new Date(),
    }),
    actions: {
        async getPlayerEvents() {
            this.initialLoader = true;
            const playerStore = usePlayerStore();
            let player_id = playerStore.player.id;
            try {
                const res = await apiService.get(
                    `/api/players/${player_id}/events`
                );
                this.events = res?.data?.events;
                // this.events = res?.data?.events.filter(
                //     (event) => !recurringIds.has(event.id)
                // );
                this.recurring = res?.data?.recurring;
            } catch (error) {
                console.log(error);
            } finally {
                this.initialLoader = false;
            }
        },
        async addPlayerEvents(payload) {
            const playerStore = usePlayerStore();
            let player_id = playerStore.player.id;
            this.actionLoader = true;
            try {
                const res = !this.singleEvent?.id
                    ? await apiService.post(
                          `/api/players/${player_id}/events`,
                          payload
                      )
                    : await apiService.patch(
                          `/api/players/${player_id}/events/${this.singleEvent.id}`,
                          payload
                      );
                const result = res.data.data;
                this.calendarDate = moment(result.start.slice(0, 10));
                // if (this.singleEvent?.id) {
                //     const index = this.events.findIndex(
                //         (event) => event.id === this.singleEvent.id
                //     );
                //     if (index !== -1) {
                //         const newState = [...this.events];
                //         newState[index] = result;
                //         this.events = newState;
                //     }
                // } else {
                //     const momentDateStart = moment(result.start).utc(false);
                //     const momentDateEnd = moment(result.end).utc(false);

                //     this.events = [
                //         ...this.events,
                //         {
                //             ...result,
                //             start: momentDateStart.format(
                //                 "YYYY-MM-DD HH:mm:ss"
                //             ),
                //             end: momentDateEnd.format("YYYY-MM-DD HH:mm:ss"),
                //         },
                //     ];
                // }
                toast.success(`Event has been updated successfully`);
                this.getPlayerEvents();
                return Promise.resolve();
            } catch (error) {
                const { response } = error;
                toast.error(response.data.message || "Something went wrong");
                throw error;
            } finally {
                this.actionLoader = false;
            }
        },
        async deleteEvent(id, type) {
            const playerStore = usePlayerStore();
            let player_id = playerStore.player.id;
            this.actionLoader = true;
            try {
                await apiService.delete(
                    `/api/players/${player_id}/events/${id}?type=${type}&date=${moment(
                        this.calendarDate
                    ).format("YYYY-MM-DD")}`
                );
                this.getPlayerEvents();
                toast.success(`Event has been deleted successfully`);
            } catch (errors) {
                const { response } = errors;
                toast.error(response?.data?.message || "Something went wrong");
            } finally {
                this.actionLoader = false;
            }
        },
    },
});
