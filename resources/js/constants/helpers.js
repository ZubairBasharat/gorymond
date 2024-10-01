import Swal from "sweetalert2";
import moment from "moment";
export const ConfirmDeleteModule = (onConfirm, title, data, type) => {
    Swal.fire({
        icon: "warning",
        text: `Are you sure you want to delete ${title}`,
        showCancelButton: true,
        confirmButtonText: "Yes Delete it",
        confirmButtonColor: "#ff9e27",
        allowOutsideClick: () => !Swal.isLoading(),
    }).then(async (result) => {
        if (result.isConfirmed) {
            if (data) {
                if (type) onConfirm(data, type);
                else onConfirm(data);
            } else {
                onConfirm();
            }
        }
    });
};

export const scrollToBottom = () => {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: "smooth",
    });
};

export function changeDateKeepingTime(dateTimeString, newDateString) {
    const originalDateTime = moment(dateTimeString.start);
    const originalEndDateTime = moment(dateTimeString.end);
    const newDate = moment(newDateString);
    const getDiffInDays = moment
        .duration(moment(dateTimeString.end).diff(dateTimeString.start))
        .asDays();

    originalDateTime.year(newDate.year());
    originalDateTime.month(newDate.month());
    originalDateTime.date(newDate.date());
    const endDate = moment(originalDateTime).add(getDiffInDays, "days");
    originalEndDateTime.year(endDate.year());
    originalEndDateTime.month(endDate.month());
    originalEndDateTime.date(endDate.date());

    return {
        start: originalDateTime.format(),
        end: originalEndDateTime.format(),
    };
}
