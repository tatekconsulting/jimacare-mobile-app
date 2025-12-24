import { useState } from "react";
import {
  User,
  Settings,
  Bell,
  Shield,
  HelpCircle,
  LogOut,
  ChevronRight,
  Camera,
  CreditCard,
  FileText,
  Star,
} from "lucide-react";
import { MobileLayout } from "@/components/mobile/MobileLayout";
import { BottomNav } from "@/components/mobile/BottomNav";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { useNavigate } from "react-router-dom";
import { toast } from "sonner";

const menuItems = [
  {
    section: "Account",
    items: [
      { icon: User, label: "Personal Information", href: "/profile/edit" },
      { icon: CreditCard, label: "Payment Methods", href: "/profile/payments" },
      { icon: Bell, label: "Notifications", href: "/profile/notifications" },
      { icon: Shield, label: "Privacy & Security", href: "/profile/privacy" },
    ],
  },
  {
    section: "Support",
    items: [
      { icon: HelpCircle, label: "Help & Support", href: "/helpdesk" },
      { icon: FileText, label: "Terms & Conditions", href: "/terms" },
      { icon: Star, label: "Rate the App", action: "rate" },
    ],
  },
];

export default function ProfileScreen() {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState("profile");
  
  // Mock user data
  const user = {
    name: "John Smith",
    email: "john.smith@email.com",
    phone: "+44 7700 900123",
    image: null,
    memberSince: "January 2024",
  };

  const handleLogout = () => {
    toast.success("Logged out successfully");
    navigate("/auth");
  };

  const handleMenuClick = (item: { href?: string; action?: string }) => {
    if (item.action === "rate") {
      toast.success("Thanks for rating JimaCare!");
    } else if (item.href) {
      navigate(item.href);
    }
  };

  return (
    <MobileLayout>
      {/* Header */}
      <header className="bg-primary text-primary-foreground safe-area-top">
        <div className="px-4 py-6">
          <div className="flex items-center gap-4">
            <div className="relative">
              <Avatar className="h-20 w-20 border-4 border-primary-foreground/20">
                <AvatarImage src={user.image || undefined} />
                <AvatarFallback className="bg-primary-foreground/20 text-primary-foreground text-xl font-bold">
                  {user.name.charAt(0)}
                </AvatarFallback>
              </Avatar>
              <button className="absolute bottom-0 right-0 w-8 h-8 bg-card rounded-full flex items-center justify-center shadow-md">
                <Camera className="h-4 w-4 text-foreground" />
              </button>
            </div>
            <div className="flex-1">
              <h1 className="text-xl font-bold">{user.name}</h1>
              <p className="text-primary-foreground/80 text-sm">{user.email}</p>
              <Badge variant="secondary" className="mt-2 bg-primary-foreground/20 text-primary-foreground border-0">
                Member since {user.memberSince}
              </Badge>
            </div>
          </div>
        </div>
      </header>

      <main className="pb-24 px-4 -mt-4">
        {/* Quick Actions */}
        <Card className="p-4 mb-4">
          <div className="grid grid-cols-3 gap-4 text-center">
            <button 
              className="flex flex-col items-center gap-2"
              onClick={() => navigate("/favorites")}
            >
              <div className="w-12 h-12 rounded-full bg-accent flex items-center justify-center">
                <Star className="h-5 w-5 text-primary" />
              </div>
              <span className="text-xs text-foreground">Favorites</span>
            </button>
            <button 
              className="flex flex-col items-center gap-2"
              onClick={() => navigate("/messages")}
            >
              <div className="w-12 h-12 rounded-full bg-accent flex items-center justify-center">
                <FileText className="h-5 w-5 text-primary" />
              </div>
              <span className="text-xs text-foreground">Bookings</span>
            </button>
            <button 
              className="flex flex-col items-center gap-2"
              onClick={() => navigate("/profile/notifications")}
            >
              <div className="w-12 h-12 rounded-full bg-accent flex items-center justify-center">
                <Settings className="h-5 w-5 text-primary" />
              </div>
              <span className="text-xs text-foreground">Settings</span>
            </button>
          </div>
        </Card>

        {/* Menu Sections */}
        {menuItems.map((section) => (
          <div key={section.section} className="mb-4">
            <h3 className="text-sm font-medium text-muted-foreground mb-2 px-1">
              {section.section}
            </h3>
            <Card className="divide-y divide-border">
              {section.items.map((item) => (
                <button
                  key={item.label}
                  className="w-full flex items-center gap-3 p-4 hover:bg-accent/50 transition-colors"
                  onClick={() => handleMenuClick(item)}
                >
                  <div className="w-10 h-10 rounded-full bg-accent flex items-center justify-center shrink-0">
                    <item.icon className="h-5 w-5 text-primary" />
                  </div>
                  <span className="flex-1 text-left text-foreground">{item.label}</span>
                  <ChevronRight className="h-5 w-5 text-muted-foreground" />
                </button>
              ))}
            </Card>
          </div>
        ))}

        {/* Logout Button */}
        <Button
          variant="outline"
          className="w-full mt-4 text-destructive border-destructive/30 hover:bg-destructive/10"
          onClick={handleLogout}
        >
          <LogOut className="h-5 w-5 mr-2" />
          Log Out
        </Button>

        {/* App Version */}
        <p className="text-center text-xs text-muted-foreground mt-6">
          JimaCare v1.0.0
        </p>
      </main>

      <BottomNav activeTab={activeTab} onTabChange={setActiveTab} />
    </MobileLayout>
  );
}
