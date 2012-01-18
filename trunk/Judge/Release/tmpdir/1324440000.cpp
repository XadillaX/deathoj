#include <iostream>
#include <queue>
#include <memory.h>
using namespace std; 
#define MAX 1000001
int num[MAX];
int ans[MAX];
struct Node
{
       int val;
       int index;
}que[MAX];
void getmin(int n,int k)
{
     int i;
     int head=1;
     int tail=0;
     for (i=1;i<k;i++)
     {
         while (head<=tail&&que[tail].val>num[i]) --tail;
         ++tail;
         que[tail].val=num[i];
         que[tail].index=i;
     }
     for (i=k;i<=n;i++)
     {
         while (head<=tail&&que[tail].val>num[i]) --tail;
         ++tail;
         que[tail].val=num[i];
         que[tail].index=i;
         while (que[head].index<i-k+1) ++head;//i-que[head].index>=k
         ans[i-k+1]=que[head].val;
     }
}
void getmax(int n,int k)
{
     int i;
     int head=1;
     int tail=0;
     for (i=1;i<k;i++)
     {
         while (head<=tail&&que[tail].val<num[i]) --tail;
         ++tail;
         que[tail].val=num[i];
         que[tail].index=i;
     }
     for (i=k;i<=n;i++)
     {
         while (head<=tail&&que[tail].val<num[i]) --tail;
         ++tail;
         que[tail].val=num[i];
         que[tail].index=i;
         while (que[head].index<i-k+1) ++head;
         ans[i-k+1]=que[head].val;
     }
}
int main()
{
    int n,k;
    while (scanf("%d%d",&n,&k)==2)
    {
          int i;
          for (i=1;i<=n;i++)
              scanf("%d",&num[i]);
          getmin(n,k);
          for (i=1;i<=n-k+1;i++)
              printf("%d ",ans[i]);
          printf("\n");
          getmax(n,k);
          for (i=1;i<=n-k+1;i++)
              printf("%d ",ans[i]);
          printf("\n");
    }
    return 0;
}