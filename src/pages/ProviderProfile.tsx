import { useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import {
  ChevronLeft,
  Heart,
  Share2,
  MapPin,
  Star,
  Shield,
  Clock,
  MessageCircle,
  Phone,
  CheckCircle2,
  Calendar,
  Briefcase,
} from "lucide-react";
import { MobileLayout } from "@/components/mobile/MobileLayout";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";

import heroImage from "@/assets/hero-carer.jpg";

const mockProvider = {
  id: "1",
  name: "Sarah Johnson",
  role: "Professional Carer",
  image: heroImage,
  rating: 4.9,
  reviewCount: 127,
  hourlyRate: 18,
  distance: "1.2 mi",
  verified: true,
  location: "London, UK",
  experience: "10+ years",
  badges: ["First Aid", "CQC Registered", "Enhanced DBS", "CPR Trained"],
  about: "I am a passionate and dedicated carer with over 10 years of experience in providing high-quality care to elderly individuals. I am trained in first aid, CPR, and have an enhanced DBS check. I believe in treating every client with dignity and respect while ensuring their safety and well-being.",
  availability: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
  services: ["Personal Care", "Medication Management", "Companionship", "Light Housekeeping", "Meal Preparation"],
  reviews: [
    {
      id: "1",
      name: "John Scott",
      date: "12 October 2023",
      rating: 5,
      comment: "Sarah is absolutely wonderful! She takes great care of my mother and treats her with so much kindness and respect. Highly recommend!",
    },
    {
      id: "2",
      name: "Emma Thompson",
      date: "28 September 2023",
      rating: 5,
      comment: "Professional, punctual, and caring. Sarah has been a blessing for our family. She goes above and beyond every time.",
    },
    {
      id: "3",
      name: "David Wilson",
      date: "15 September 2023",
      rating: 4,
      comment: "Great carer, very reliable. My father looks forward to her visits. Would definitely recommend to others.",
    },
  ],
};

export default function ProviderProfile() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [isFavorite, setIsFavorite] = useState(false);
  const provider = mockProvider;

  return (
    <MobileLayout>
      {/* Hero Image */}
      <div className="relative h-72">
        <img
          src={provider.image}
          alt={provider.name}
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent" />
        
        {/* Top Navigation */}
        <div className="absolute top-0 left-0 right-0 p-4 flex items-center justify-between safe-area-top">
          <Button
            variant="secondary"
            size="icon"
            className="rounded-full bg-card/80 backdrop-blur-sm"
            onClick={() => navigate(-1)}
          >
            <ChevronLeft className="h-5 w-5" />
          </Button>
          <div className="flex gap-2">
            <Button
              variant="secondary"
              size="icon"
              className="rounded-full bg-card/80 backdrop-blur-sm"
            >
              <Share2 className="h-5 w-5" />
            </Button>
            <Button
              variant="secondary"
              size="icon"
              className={`rounded-full bg-card/80 backdrop-blur-sm ${
                isFavorite ? "text-destructive" : ""
              }`}
              onClick={() => setIsFavorite(!isFavorite)}
            >
              <Heart className={`h-5 w-5 ${isFavorite ? "fill-current" : ""}`} />
            </Button>
          </div>
        </div>

        {/* Provider Info Overlay */}
        <div className="absolute bottom-0 left-0 right-0 p-4">
          <div className="flex items-center gap-2 mb-1">
            {provider.verified && (
              <Badge variant="verified" className="gap-1">
                <Shield className="h-3 w-3" />
                Verified
              </Badge>
            )}
          </div>
          <h1 className="text-2xl font-bold text-foreground">{provider.name}</h1>
          <p className="text-muted-foreground">{provider.role}</p>
        </div>
      </div>

      <main className="px-4 pb-32 -mt-2 relative z-10">
        {/* Quick Stats */}
        <Card className="p-4 mb-4">
          <div className="grid grid-cols-3 gap-4 text-center">
            <div>
              <div className="flex items-center justify-center gap-1 text-lg font-semibold text-foreground">
                <Star className="h-5 w-5 text-warning fill-warning" />
                {provider.rating}
              </div>
              <p className="text-xs text-muted-foreground">{provider.reviewCount} reviews</p>
            </div>
            <div>
              <div className="text-lg font-semibold text-foreground">£{provider.hourlyRate}</div>
              <p className="text-xs text-muted-foreground">per hour</p>
            </div>
            <div>
              <div className="flex items-center justify-center gap-1 text-lg font-semibold text-foreground">
                <MapPin className="h-4 w-4 text-primary" />
                {provider.distance}
              </div>
              <p className="text-xs text-muted-foreground">away</p>
            </div>
          </div>
        </Card>

        {/* Experience & Availability */}
        <div className="flex gap-3 mb-4">
          <Card className="flex-1 p-3">
            <div className="flex items-center gap-2">
              <Briefcase className="h-5 w-5 text-primary" />
              <div>
                <p className="text-sm font-medium text-foreground">{provider.experience}</p>
                <p className="text-xs text-muted-foreground">Experience</p>
              </div>
            </div>
          </Card>
          <Card className="flex-1 p-3">
            <div className="flex items-center gap-2">
              <Clock className="h-5 w-5 text-primary" />
              <div>
                <p className="text-sm font-medium text-foreground">Flexible</p>
                <p className="text-xs text-muted-foreground">Availability</p>
              </div>
            </div>
          </Card>
        </div>

        {/* Badges */}
        <div className="flex flex-wrap gap-2 mb-6">
          {provider.badges.map((badge) => (
            <Badge key={badge} variant="secondary" className="gap-1">
              <CheckCircle2 className="h-3 w-3 text-success" />
              {badge}
            </Badge>
          ))}
        </div>

        {/* Tabs */}
        <Tabs defaultValue="about" className="mb-6">
          <TabsList className="grid w-full grid-cols-3">
            <TabsTrigger value="about">About</TabsTrigger>
            <TabsTrigger value="services">Services</TabsTrigger>
            <TabsTrigger value="reviews">Reviews</TabsTrigger>
          </TabsList>

          <TabsContent value="about" className="mt-4">
            <Card className="p-4">
              <h3 className="font-semibold text-foreground mb-2">About Me</h3>
              <p className="text-sm text-muted-foreground leading-relaxed">
                {provider.about}
              </p>

              <h3 className="font-semibold text-foreground mt-4 mb-2">Availability</h3>
              <div className="flex flex-wrap gap-2">
                {provider.availability.map((day) => (
                  <Badge key={day} variant="outline">
                    {day}
                  </Badge>
                ))}
              </div>
            </Card>
          </TabsContent>

          <TabsContent value="services" className="mt-4">
            <Card className="p-4">
              <h3 className="font-semibold text-foreground mb-3">Services Offered</h3>
              <div className="space-y-3">
                {provider.services.map((service) => (
                  <div key={service} className="flex items-center gap-3">
                    <CheckCircle2 className="h-5 w-5 text-success" />
                    <span className="text-sm text-foreground">{service}</span>
                  </div>
                ))}
              </div>
            </Card>
          </TabsContent>

          <TabsContent value="reviews" className="mt-4 space-y-3">
            {provider.reviews.map((review) => (
              <Card key={review.id} className="p-4">
                <div className="flex items-center justify-between mb-2">
                  <div>
                    <p className="font-medium text-foreground">{review.name}</p>
                    <p className="text-xs text-muted-foreground">{review.date}</p>
                  </div>
                  <div className="flex items-center gap-1">
                    <Star className="h-4 w-4 text-warning fill-warning" />
                    <span className="text-sm font-medium">{review.rating}</span>
                  </div>
                </div>
                <p className="text-sm text-muted-foreground leading-relaxed">
                  {review.comment}
                </p>
              </Card>
            ))}
          </TabsContent>
        </Tabs>
      </main>

      {/* Fixed Bottom CTA */}
      <div className="fixed bottom-0 left-0 right-0 bg-background/95 backdrop-blur-sm border-t border-border p-4 safe-area-bottom">
        <div className="max-w-md mx-auto flex gap-3">
          <Button variant="outline" size="lg" className="flex-1 gap-2">
            <Phone className="h-5 w-5" />
            Call
          </Button>
          <Button size="lg" className="flex-1 gap-2">
            <MessageCircle className="h-5 w-5" />
            Message
          </Button>
        </div>
      </div>
    </MobileLayout>
  );
}
