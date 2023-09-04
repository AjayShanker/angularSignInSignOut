export class Users {
    public id: number;
    public first_name: string;
    public last_name:string;
    public email:string;
    public phone: string;
    public organization: string;
    public otp: string;
    public meeting_id: string;
    public is_active: string;
    public ipaddress:string;

    constructor(id:number, first_name:string, last_name:string, email:string, phone:string, organization:string, otp: string, meeting_id:string, is_active:string, ipaddress: string) {
                this.id = id;
                this.first_name = first_name;
                this.last_name = last_name;
                this.email = email;
                this.phone = phone;
                this.organization = organization;
                this.otp = otp;
                this.meeting_id = meeting_id;
                this.is_active = is_active;
                this.ipaddress = ipaddress;
    }
}