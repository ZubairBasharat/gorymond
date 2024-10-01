import { defineStore } from "pinia";
import apiService from "../service/apiService";
import { toast } from "../service/toastWrapper";

export const usePlayerStore = defineStore("player", {
    state: () => ({
        player: null,
        pageLoader: true,
        actionLoader: false,
        playerConfig: null,
        allLocations: [],
    }),
    actions: {
        async handleCreatePlayer(data) {
            this.actionLoader = true;
            try {
                const res = await apiService.post("/api/players", data);
                this.player = res?.data?.data;
                toast.success("Player has been created successfully");
                setTimeout(() => {
                    this.router.push("/dashboard");
                }, 1000);
            } catch (errors) {
                const { response } = errors;
                const errorsKeys = response?.data?.errors;
                if (errorsKeys && Object.keys(errorsKeys).length > 0) {
                    Object.keys(errorsKeys).forEach((key) => {
                        toast.error(errorsKeys[key]);
                    });
                } else {
                    toast.error(
                        response?.data?.message || "Something went wrong"
                    );
                }
            } finally {
                this.actionLoader = false;
            }
        },
        async updatePlayerContactAvatar(payload) {
            this.actionLoader = true;
            const formData = new FormData();
            formData.append("avatar", payload.avatar);
            try {
                const res = await apiService.post(
                    `/api/players/${this.playerConfig.id}/contacts/${payload.id}/avatar`,
                    formData
                );
                const index = this.playerConfig.contacts.findIndex(
                    (contact) => contact.id === payload.id
                );
                if (index !== -1) {
                    this.playerConfig.contacts[index] = {
                        ...this.playerConfig.contacts[index],
                        ...res.data.data,
                    };
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.actionLoader = false;
            }
        },
        async addPlayerContact(data, id) {
            this.actionLoader = true;
            try {
                const payload = { ...data };
                if (data?.avatar) {
                    delete payload.avatar;
                }
                const res = !id
                    ? await apiService.post(
                          `/api/players/${this.playerConfig.id}/contacts`,
                          payload
                      )
                    : await apiService.patch(
                          `/api/players/${this.playerConfig.id}/contacts/${id}`,
                          payload
                      );
                if (!id) {
                    this.playerConfig.contacts = [
                        ...this.playerConfig.contacts,
                        res.data,
                    ];
                } else {
                    const index = this.playerConfig.contacts.findIndex(
                        (contact) => contact.id === id
                    );
                    if (index !== -1) {
                        this.playerConfig.contacts[index] = {
                            ...this.playerConfig.contacts[index],
                            ...res.data.data,
                        };
                    }
                }
                toast.success(
                    `Player has been ${
                        !id ? "created" : "updated"
                    } successfully`
                );
                if (data?.avatar) {
                    this.updatePlayerContactAvatar({
                        id: !data ? res.data.id : res.data.data.id,
                        avatar: data.avatar,
                    });
                }
                return Promise.resolve();
            } catch (errors) {
                const { response } = errors;
                const errorsKeys = response?.data?.errors;
                if (errorsKeys && Object.keys(errorsKeys).length > 0) {
                    Object.keys(errorsKeys).forEach((key) => {
                        toast.error(errorsKeys[key]);
                    });
                } else {
                    toast.error(
                        response?.data?.message || "Something went wrong"
                    );
                }
                throw errors;
            } finally {
                this.actionLoader = false;
            }
        },
        async updatePlayerAvatar(payload) {
            this.actionLoader = true;
            const formData = new FormData();
            formData.append("avatar", payload.files[0]);
            try {
                const res = await apiService.post(
                    `/api/players/${this.playerConfig.id}/avatar`,
                    formData
                );
                const result = res.data.data;
                this.playerConfig = {
                    ...this.playerConfig,
                    avatar_url: result.avatar_url,
                    avatar: result.avatar,
                };
            } catch (error) {
                console.log(error);
            } finally {
                this.actionLoader = false;
            }
        },
        async getPlayers() {
            this.pageLoader = true;
            try {
                const res = await apiService.get("/api/players");

                console.log("res", res);
                this.player = res?.data?.data;
            } catch (error) {
                console.log(error);
            } finally {
                this.pageLoader = false;
            }
        },
        async deletePlayerContact(id) {
            this.actionLoader = true;
            try {
                const res = await apiService.delete(
                    `/api/players/${this.playerConfig.id}/contacts/${id}`
                );
                this.playerConfig.contacts = this.playerConfig.contacts.filter(
                    (x) => x.id !== id
                );
                toast.success("Player contact has been deleted successfully");
            } catch (errors) {
                const { response } = errors;
                toast.error(response?.data?.message || "Something went wrong");
            } finally {
                this.actionLoader = false;
            }
        },
        async getPlayerConfig(id) {
            this.pageLoader = true;
            try {
                const res = await apiService.get(
                    `/api/players/configuration/${id}`
                );
                this.playerConfig = res?.data?.data;
            } catch (error) {
                console.log(error);
            } finally {
                this.pageLoader = false;
            }
        },
        async updatePlayerConfig(payload) {
            this.actionLoader = true;
            try {
                const res = await apiService.put(
                    `/api/players/${payload.id}`,
                    payload
                );
                this.playerConfig = {
                    ...this.playerConfig,
                    ...res?.data?.data,
                };
                toast.success("Player config has been updated successfully");
            } catch (errors) {
                const { response } = errors;
                toast.error(response?.data?.message || "Something went wrong");
            } finally {
                this.actionLoader = false;
            }
        },

        async getLocations(id) {
            this.pageLoader = true;
            try {
                // const res = await apiService.get(`/api/locations/${id}`);
                const res = await apiService.get(
                    `/api/players/${id}/locations`
                );
                console.log("res", res);
                this.allLocations = res?.data?.data;
            } catch (error) {
                this.allLocations = [];
                // console.log(error);
            } finally {
                this.pageLoader = false;
            }
        },

        async handleSaveLocation(data) {
            console.log("handleSaveLocation data", data);

            const player_id = data.player_id;

            const updateRes = await apiService.post(
                `/api/players/${player_id}/locations`,
                data
            );

            console.log("updateRes", updateRes);

            // toast.success(
            //     "future message for : location has been updated successfully"
            // );

            try {
                if (updateRes.data.mode === "save") {
                    const newLocation = { ...updateRes.data.data };

                    this.allLocations.push(newLocation);
                } else {
                    // update the location in the array
                    const location_id = updateRes.data.data.id;

                    const index = this.allLocations.findIndex(
                        (location) => location.id === location_id
                    );
                    if (index !== -1) {
                        this.allLocations[index] = updateRes.data.data;
                    }
                }
            } catch (errors) {
                const { response } = errors;
                const errorsKeys = response?.data?.errors;
                if (errorsKeys && Object.keys(errorsKeys).length > 0) {
                    Object.keys(errorsKeys).forEach((key) => {
                        toast.error(errorsKeys[key]);
                    });
                }
            }
        },

        // am close to deleting this...
        async handleUpdateLocation(data) {
            // toast.success(
            //     "future message for : location has been updated successfully"
            // );

            const player_id = data.player_id;

            try {
                // cant make put request work (error 405 method not allowed)
                // so we call with post, and look for _method == PUT in the data
                // in the controller

                // const updateRes = await apiService.put(
                const updateRes = await apiService.post(
                    `/api/players/${player_id}/locations`,
                    data
                );
            } catch (errors) {
                const { response } = errors;
                const errorsKeys = response?.data?.errors;
                if (errorsKeys && Object.keys(errorsKeys).length > 0) {
                    Object.keys(errorsKeys).forEach((key) => {
                        toast.error(errorsKeys[key]);
                    });
                }
            }
        },

        async handleDeleteLocation(data) {
            const player_id = data.player_id;
            const location_id = data.location_id; // Ensure location_id is part of data

            try {
                const deleteRes = await apiService.delete(
                    `/api/players/${player_id}/locations/${location_id}`
                );
            } catch (errors) {
                console.log("errors", errors);
                const { response } = errors;
                const errorsKeys = response?.data?.errors;
                if (errorsKeys && Object.keys(errorsKeys).length > 0) {
                    Object.keys(errorsKeys).forEach((key) => {
                        toast.error(errorsKeys[key]);
                    });
                }
            }
        },

        async handleCancel() {
            this.router.go(-1);
        },
    },
});
