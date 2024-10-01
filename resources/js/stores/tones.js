import { defineStore } from "pinia";
import apiService from "../service/apiService";
import { toast } from "../service/toastWrapper";

import { usePlayerStore } from "./player";
export const useTonesStore = defineStore("tones", {
    state: () => ({
        tones: [],
        playerTones: null,
        actionLoader: false,
    }),
    actions: {
        async getTones() {
            this.actionLoader = true;
            try {
                const res = await apiService.get(`/api/tones`);
                this.tones = res?.data?.data;
            } catch (error) {
                console.log(error);
            } finally {
                this.actionLoader = false;
            }
        },
        async getPlayerTones() {
            const playerStore = usePlayerStore();
            let player_id = playerStore.player.id;
            this.actionLoader = true;
            try {
                const res = await apiService.get(
                    `/api/players/${player_id}/tones`
                );
                this.playerTones = res?.data?.data;
            } catch (error) {
                console.log(error);
            } finally {
                this.actionLoader = false;
            }
        },
        async updatePlayerTones(payload) {
            console.log(payload);
            const playerStore = usePlayerStore();
            let player_id = playerStore.player.id;
            this.actionLoader = true;
            try {
                await apiService.post(
                    `/api/players/${player_id}/tones`,
                    payload
                );
                toast.success(
                    `${payload.model} tones has been updated successfully`
                );
            } catch (error) {
                const { response } = error;
                toast.error(response.data.message || "Something went wrong");
            } finally {
                this.actionLoader = false;
            }
        },
    },
});
