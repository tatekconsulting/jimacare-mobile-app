import { useState } from "react";
import { MessageCircle, Search } from "lucide-react";
import { MobileLayout } from "@/components/mobile/MobileLayout";
import { BottomNav } from "@/components/mobile/BottomNav";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { useNavigate } from "react-router-dom";

import heroImage from "@/assets/hero-carer.jpg";
import childminderImage from "@/assets/childminder.jpg";

const mockConversations = [
  {
    id: "1",
    name: "Sarah Johnson",
    role: "Professional Carer",
    image: heroImage,
    lastMessage: "Thank you for booking! I'll see you on Monday at 9am.",
    timestamp: "2 min ago",
    unread: 2,
    online: true,
  },
  {
    id: "2",
    name: "Emily Chen",
    role: "Childminder",
    image: childminderImage,
    lastMessage: "Sure, I can accommodate the extra hour. No problem!",
    timestamp: "1 hour ago",
    unread: 0,
    online: false,
  },
];

export default function MessagesScreen() {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState("messages");
  const [searchQuery, setSearchQuery] = useState("");

  const filteredConversations = mockConversations.filter((conv) =>
    conv.name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <MobileLayout>
      {/* Header */}
      <header className="sticky top-0 z-40 bg-background/95 backdrop-blur-sm border-b border-border safe-area-top">
        <div className="px-4 py-4">
          <h1 className="text-xl font-bold text-foreground mb-3">Messages</h1>
          
          {/* Search */}
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search messages..."
              className="pl-9 h-10"
            />
          </div>
        </div>
      </header>

      <main className="pb-24 px-4">
        {filteredConversations.length > 0 ? (
          <div className="space-y-2 py-4">
            {filteredConversations.map((conversation) => (
              <Card
                key={conversation.id}
                variant="interactive"
                className="p-4"
                onClick={() => navigate(`/chat/${conversation.id}`)}
              >
                <div className="flex items-start gap-3">
                  <div className="relative">
                    <Avatar className="h-12 w-12">
                      <AvatarImage src={conversation.image} alt={conversation.name} />
                      <AvatarFallback>{conversation.name.charAt(0)}</AvatarFallback>
                    </Avatar>
                    {conversation.online && (
                      <span className="absolute bottom-0 right-0 w-3 h-3 bg-success border-2 border-card rounded-full" />
                    )}
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between mb-1">
                      <h3 className="font-semibold text-foreground truncate">
                        {conversation.name}
                      </h3>
                      <span className="text-xs text-muted-foreground shrink-0">
                        {conversation.timestamp}
                      </span>
                    </div>
                    <p className="text-xs text-muted-foreground mb-1">
                      {conversation.role}
                    </p>
                    <div className="flex items-center justify-between">
                      <p className="text-sm text-muted-foreground truncate pr-2">
                        {conversation.lastMessage}
                      </p>
                      {conversation.unread > 0 && (
                        <Badge className="shrink-0 h-5 w-5 p-0 flex items-center justify-center text-xs">
                          {conversation.unread}
                        </Badge>
                      )}
                    </div>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        ) : (
          <div className="flex flex-col items-center justify-center py-16 text-center">
            <div className="w-20 h-20 rounded-full bg-accent flex items-center justify-center mb-4">
              <MessageCircle className="h-10 w-10 text-primary" />
            </div>
            <h2 className="text-lg font-semibold text-foreground mb-2">
              No messages yet
            </h2>
            <p className="text-sm text-muted-foreground mb-6 max-w-xs">
              Start a conversation with a care provider to see your messages here
            </p>
            <Button onClick={() => navigate("/search")}>
              Find Providers
            </Button>
          </div>
        )}
      </main>

      <BottomNav activeTab={activeTab} onTabChange={setActiveTab} />
    </MobileLayout>
  );
}
