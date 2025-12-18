import { Config } from 'ziggy-js';

export interface Branch {
    id: number;
    name: string;
    code: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    role: 'admin' | 'manager' | 'staff' | 'customer';
    branch_id?: number;
    branch?: Branch;
    is_customer: boolean;
    loyalty_points?: number;
    loyalty_tier?: 'bronze' | 'silver' | 'gold' | 'platinum';
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};
