export interface User {
  id: string;
  name: string;
  email: string;
  role: 'admin' | 'manager' | 'operator';
  avatar?: string;
}

export interface Campaign {
  id: string;
  name: string;
  type: 'sms' | 'whatsapp' | 'email' | 'push';
  status: 'draft' | 'active' | 'paused' | 'completed';
  sent: number;
  delivered: number;
  opened: number;
  clicked: number;
  createdAt: string;
  scheduledAt?: string;
}

export interface Analytics {
  totalCampaigns: number;
  totalSent: number;
  deliveryRate: number;
  openRate: number;
  clickRate: number;
  revenue: number;
}

export interface Contact {
  id: string;
  name: string;
  phone: string;
  email?: string;
  tags: string[];
  createdAt: string;
  lastActivity: string;
}